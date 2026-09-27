<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ApplicationStatusHistory> */
class ApplicationStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'changed_by' => User::factory(),
            'old_status' => ApplicationStatus::APPLIED,
            'new_status' => ApplicationStatus::IN_REVIEW,
            'notes' => $this->faker->sentence(),
        ];
    }
}
