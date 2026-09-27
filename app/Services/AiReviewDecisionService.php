<?php

namespace App\Services;

use App\Enums\AiReviewAction;
use App\Enums\AiReviewStatus;
use App\Models\AiReviewItem;
use App\Models\CvExtraction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AiReviewDecisionService
{
    public function __construct(private CvExtractionReviewHandler $cvHandler) {}

    /** @param array<string, mixed> $data */
    public function approve(AiReviewItem $item, User $reviewer, array $data): AiReviewItem
    {
        return $this->decide($item, $reviewer, AiReviewAction::APPROVED, $data);
    }

    /** @param array<string, mixed> $data */
    public function correct(AiReviewItem $item, User $reviewer, array $data): AiReviewItem
    {
        return $this->decide($item, $reviewer, AiReviewAction::CORRECTED, $data);
    }

    /** @param array<string, mixed> $data */
    public function reject(AiReviewItem $item, User $reviewer, array $data): AiReviewItem
    {
        return $this->decide($item, $reviewer, AiReviewAction::REJECTED, $data);
    }

    /** @param array<string, mixed> $data */
    private function decide(AiReviewItem $item, User $reviewer, AiReviewAction $action, array $data): AiReviewItem
    {
        return DB::transaction(function () use ($item, $reviewer, $action, $data): AiReviewItem {
            $locked = AiReviewItem::query()->lockForUpdate()->findOrFail($item->id);
            if (! $locked->isPending()) {
                throw ValidationException::withMessages(['review_item' => __('ai_review.invalid_state')]);
            }

            $extraction = CvExtraction::query()->lockForUpdate()->findOrFail($locked->entity_id);
            $canonicalBefore = $this->cvHandler->snapshot($extraction);
            $appliedValue = null;
            if ($action === AiReviewAction::APPROVED) {
                $appliedValue = $this->cvHandler->apply($extraction, $locked->proposed_value);
            } elseif ($action === AiReviewAction::CORRECTED) {
                $correctedValue = $data['corrected_value'] ?? null;
                if (! is_array($correctedValue)) {
                    throw ValidationException::withMessages(['corrected_value' => __('ai_review.invalid_correction')]);
                }
                $appliedValue = $this->cvHandler->apply($extraction, $correctedValue);
            }

            $locked->update([
                'status' => AiReviewStatus::from($action->value),
                'reviewer_id' => $reviewer->id,
                'reviewed_at' => now(),
                'decision_reason' => $data['decision_reason'] ?? null,
                'reviewer_notes' => $data['reviewer_notes'] ?? null,
                'corrected_value' => $action === AiReviewAction::CORRECTED ? $appliedValue : null,
            ]);

            $locked->audits()->create([
                'action' => $action,
                'reviewer_id' => $reviewer->id,
                'original_proposal' => $locked->proposed_value,
                'canonical_before' => $canonicalBefore,
                'applied_value' => $appliedValue,
                'decision_reason' => $data['decision_reason'] ?? null,
                'reviewer_notes' => $data['reviewer_notes'] ?? null,
            ]);

            return $locked->load(['reviewer:id,name', 'audits']);
        }, 3);
    }
}
