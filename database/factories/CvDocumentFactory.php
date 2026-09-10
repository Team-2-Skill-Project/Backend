<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\CvDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvDocument>
 */
class CvDocumentFactory extends Factory
{
    protected $model = CvDocument::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'candidate_profile_id' => CandidateProfile::factory(),
            'original_filename' => fake()->word().'.pdf',
            'storage_disk' => 'local',
            'storage_path' => 'cvs/'.fake()->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(50_000, 2_000_000),
            'file_hash' => hash('sha256', fake()->uuid()),
            'version' => 1,
            'is_current' => true,
            'status' => 'uploaded',
            'processed_at' => null,
            'failure_reason' => null,
        ];
    }
}
