<?php

namespace Database\Factories;

use App\Models\CvDocument;
use App\Models\CvExtraction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvExtraction>
 */
class CvExtractionFactory extends Factory
{
    protected $model = CvExtraction::class;

    public function definition(): array
    {
        return [
            'cv_document_id' => CvDocument::factory(),
            'attempt_number' => 1,
            'status' => 'pending',
            'provider' => null,
            'model' => null,
            'parser_version' => null,
            'raw_text' => null,
            'extracted_data' => null,
            'confidence_score' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
