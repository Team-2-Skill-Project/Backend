<?php

namespace Database\Seeders;

use App\Models\CandidateProfile;
use App\Models\CandidateSkill;
use App\Models\CareerPreference;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CandidateProfileSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SkillSeeder::class);

        $profile = CandidateProfile::factory()
            ->has(Education::factory(), 'educations')
            ->has(Experience::factory(), 'experiences')
            ->has(Project::factory(), 'projects')
            ->has(Certificate::factory(), 'certificates')
            ->has(Language::factory(), 'languages')
            ->has(CareerPreference::factory(), 'careerPreference')
            ->create();

        $profile->skills()->attach(
            Skill::query()->where('name', 'Laravel')->sole(),
            ['source' => CandidateSkill::SOURCE_MANUAL],
        );
    }
}
