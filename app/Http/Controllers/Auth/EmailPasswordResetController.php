<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailPasswordResetRequest;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailPasswordResetController extends Controller
{
    use PasswordValidationRules;

    public function store(EmailPasswordResetRequest $request, EmailOtpService $emailOtpService): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $emailOtpService): JsonResponse {
                $user = User::whereRaw('LOWER(email) = ?', [Str::lower($request->validated('email'))])->lockForUpdate()->first();

                if (! $user) {
                    return $this->invalid('email', 'Unable to send a reset code. Check the email address and try again.');
                }

                $previousOtp = $this->resetOtp($user);

                if ($previousOtp?->last_sent_at?->greaterThan(now()->subMinute())) {
                    return response()->json(['message' => 'Please wait one minute before requesting another code.'], 429)
                        ->header('Retry-After', '60');
                }

                $emailOtpService->send($user, EmailOtp::PASSWORD_RESET);

                return response()->json(['message' => 'A password reset code has been sent to your email.']);
            });
        } catch (TransportExceptionInterface) {
            return response()->json(['message' => 'Unable to send the email code. Please try again shortly.'], 503);
        }
    }

    public function verifyOtp(EmailPasswordResetRequest $request): JsonResponse
    {
        $validated = $request->validate(['otp' => ['required', 'string', 'regex:/^[0-9]{6}$/']]);

        return DB::transaction(function () use ($request, $validated): JsonResponse {
            $user = User::whereRaw('LOWER(email) = ?', [Str::lower($request->validated('email'))])->lockForUpdate()->first();
            $emailOtp = $user ? $this->resetOtp($user) : null;

            if (! $emailOtp || $emailOtp->reset_token_hash || now()->greaterThanOrEqualTo($emailOtp->expires_at)) {
                return $this->invalid('otp', 'The code is invalid or expired. Request a new code.');
            }

            if ($emailOtp->attempts >= 5) {
                return response()->json(['message' => 'Too many invalid attempts. Request a new code.'], 429);
            }

            if (! Hash::check($validated['otp'], $emailOtp->code_hash)) {
                $emailOtp->increment('attempts');

                return $this->invalid('otp', 'The code is invalid or expired. Request a new code.');
            }

            $resetToken = Str::random(64);
            $emailOtp->update([
                'reset_token_hash' => Hash::make($resetToken),
                'reset_token_expires_at' => now()->addMinutes(10),
            ]);

            return response()->json([
                'message' => 'Code verified. You may now reset your password.',
                'reset_token' => $resetToken,
                'expires_in' => 600,
            ])->header('Cache-Control', 'no-store');
        });
    }

    public function resetPassword(EmailPasswordResetRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'reset_token' => ['required', 'string', 'size:64'],
            'password' => $this->passwordRules(),
        ]);

        return DB::transaction(function () use ($request, $validated): JsonResponse {
            $user = User::whereRaw('LOWER(email) = ?', [Str::lower($request->validated('email'))])->lockForUpdate()->first();
            $emailOtp = $user ? $this->resetOtp($user) : null;

            if (! $emailOtp?->reset_token_hash || ! $emailOtp->reset_token_expires_at
                || now()->greaterThanOrEqualTo($emailOtp->reset_token_expires_at)
                || ! Hash::check($validated['reset_token'], $emailOtp->reset_token_hash)) {
                return $this->invalid('reset_token', 'The password reset session is invalid or expired. Request a new code.');
            }

            $user->password = Hash::make($validated['password']);
            $user->setRememberToken(Str::random(60));
            $user->save();

            EmailOtp::where('user_id', $user->id)->where('purpose', EmailOtp::PASSWORD_RESET)->delete();
            Password::broker(config('fortify.passwords'))->deleteToken($user);
            event(new PasswordReset($user));

            return response()->json(['message' => 'Password reset successfully. Please log in.']);
        });
    }

    private function resetOtp(User $user): ?EmailOtp
    {
        return EmailOtp::where('user_id', $user->id)
            ->where('email', $user->email)
            ->where('purpose', EmailOtp::PASSWORD_RESET)
            ->latest('id')->lockForUpdate()->first();
    }

    private function invalid(string $field, string $message): JsonResponse
    {
        return response()->json(['message' => $message, 'errors' => [$field => [$message]]], 422);
    }
}
