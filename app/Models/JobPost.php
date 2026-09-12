<?php

namespace App\Models;

use Database\Factories\JobPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    /** @use HasFactory<JobPostFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'title',
        'description',
        'employment_type',
        'work_mode',
        'experience_level',
        'country',
        'state',
        'city',
        'salary_min',
        'salary_max',
        'salary_currency',
        'application_url',
        'source',
        'external_id',
        'external_url',
        'status',
        'published_at',
        'expires_at',
        'min_years_experience',
        'max_years_experience',
        'responsibilities',
        'canonical_role',
    ];

    protected function casts(): array
    {
        return [
            'min_years_experience' => 'integer',
            'max_years_experience' => 'integer',
            'responsibilities' => 'array',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<JobSkill, $this> */
    public function jobSkills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    /** @return HasMany<JobMatch, $this> */
    public function jobMatches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }

    /** @return HasMany<Roadmap, $this> */
    public function roadmaps(): HasMany
    {
        return $this->hasMany(Roadmap::class, 'target_job_post_id');
    }
}
