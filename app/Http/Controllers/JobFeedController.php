<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobListRequest;
use App\Http\Resources\JobFeedResource;
use App\Models\JobPost;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class JobFeedController extends Controller
{
    public function __invoke(JobListRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $query = JobPost::query()
            ->active()
            ->notExpired()
            ->whereHas('company', function ($query): void {
                $query->where('is_active', true);
            })
            ->with([
                'company:id,name,logo_url,is_verified',
                'requiredSkills:id,name',
                'preferredSkills:id,name',
            ])
            ->orderByRaw('published_at IS NULL ASC')
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if (! empty($filters['search'])) {
            $search = Str::lower(Str::trim($filters['search']));
            $query->where(function ($query) use ($search): void {
                $like = "%{$search}%";
                $query->whereRaw('LOWER(job_posts.title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(job_posts.canonical_role) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(job_posts.description) LIKE ?', [$like])
                    ->orWhereHas('company', function ($query) use ($like): void {
                        $query->whereRaw('LOWER(companies.name) LIKE ?', [$like])
                            ->orWhereHas('aliases', fn ($query) => $query->whereRaw('LOWER(normalized_alias) LIKE ?', [$like]));
                    });
            });
        }

        foreach (['company_id', 'job_type', 'work_mode', 'employment_type', 'experience_level', 'country', 'state', 'city', 'source', 'application_method'] as $field) {
            if (array_key_exists($field, $filters)) {
                $query->where('job_posts.'.$field, $filters[$field]);
            }
        }

        if (array_key_exists('is_verified_company', $filters)) {
            $query->whereHas('company', fn ($query) => $query->where('is_verified', $filters['is_verified_company']));
        }

        foreach ($filters['required_skill_ids'] ?? [] as $skillId) {
            $query->whereHas('requiredSkills', fn ($query) => $query->whereKey($skillId));
        }
        foreach ($filters['preferred_skill_ids'] ?? [] as $skillId) {
            $query->whereHas('preferredSkills', fn ($query) => $query->whereKey($skillId));
        }

        return JobFeedResource::collection($query->paginate($filters['per_page'] ?? 15)->withQueryString());
    }
}
