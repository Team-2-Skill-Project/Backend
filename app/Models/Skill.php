<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'normalized_name',
        'category',
    ];

    /** @return HasMany<CandidateSkill, $this> */
    public function candidateSkills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    /** @return BelongsToMany<CandidateProfile, $this> */
    public function candidateProfiles(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'candidate_skills')
            ->withPivot(['id', 'source', 'proficiency_level'])
            ->withTimestamps();
    }
}
