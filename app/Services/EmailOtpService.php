<?php

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class EmailOtpService
{
    public function send(User $user, string $purpose): void
    {
        DB::transaction(function () use ($user, $purpose): void {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($purpose === EmailOtp::EMAIL_VERIFICATION && $user->hasVerifiedEmail()) {
                return;
            }

            $previous = EmailOtp::where('user_id', $user->id)->where('purpose', $purpose)->first();
            if ($previous?->last_sent_at?->greaterThan(now()->subMinute())) {
                throw new TooManyRequestsHttpException(60, __('auth.wait_before_new_code'));
            }

            EmailOtp::where('user_id', $user->id)->where('purpose', $purpose)->delete();
            $staticMode = $this->staticModeEnabled();
            $code = $staticMode ? (string) config('otp.static_code') : (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10);
            EmailOtp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'purpose' => $purpose,
                'code_hash' => Hash::make($code),
                'expires_at' => $expiresAt,
                'attempts' => 0,
                'last_sent_at' => now(),
            ]);

            if (! $staticMode) {
                Mail::to($user->email)->send(new EmailOtpMail($code, $purpose, $expiresAt));
            }
        });
    }

    private function staticModeEnabled(): bool
    {
        if (app()->isProduction()
            || config('app.env') === 'production'
            || ! (bool) config('otp.static_enabled', false)) {
            return false;
        }

        return preg_match('/^[0-9]{6}$/', (string) config('otp.static_code', '')) === 1;
    }
}
