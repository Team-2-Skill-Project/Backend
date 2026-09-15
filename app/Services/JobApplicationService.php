<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobApplicationService
{
    protected array $allowedTransitions = [
        'applied' => ['in_review', 'withdrawn', 'rejected'],
        'in_review' => ['interview', 'rejected', 'offer', 'withdrawn'],
        'interview' => ['offer', 'rejected', 'withdrawn'],
        'offer' => ['withdrawn'],
        'rejected' => [],
        'withdrawn' => [],
    ];

    /**
     * Create a new job application and log the initial status in the history table.
     */
    public function createApplication($candidateProfileId, array $data): Application
    {
        return DB::transaction(function () use ($candidateProfileId, $data) {
            $application = Application::create([
                'candidate_profile_id' => $candidateProfileId,
                'job_id' => $data['job_id'],
                'status' => ApplicationStatus::APPLIED->value,
                'cover_letter' => $data['cover_letter'] ?? null,
            ]);

            // add a new record to the ApplicationStatusHistory table
            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'changed_by' => auth()->id(),
                'old_status' => null,
                'new_status' => ApplicationStatus::APPLIED->value,
                'notes' => 'created new application with status applied.',
            ]);

            return $application;
        });
    }

    /**
     * Update the status of a job application and log the change in the history table.
     */
    public function updateStatus(Application $application, string $newStatus, ?string $notes = null): Application
    {
        $currentStatus = $application->status->value;

        // Check if the transition is allowed
        if (!in_array($newStatus, $this->allowedTransitions[$currentStatus] ?? [])) {
            throw ValidationException::withMessages([
                'status' => "It is not possible to transition from the state ({$currentStatus}) To state ({$newStatus})."
            ]);
        }

        return DB::transaction(function () use ($application, $currentStatus, $newStatus, $notes) {
            $oldStatus = $application->status;

            $application->update([
                'status' => $newStatus
            ]);

            // add a new record to the ApplicationStatusHistory table
            ApplicationStatusHistory::create([
                'job_application_id' => $application->id,
                'changed_by' => auth()->id(),
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus,
                'notes' => $notes ?? 'The status has been updated.',
            ]);

            return $application;
        });
    }
}
