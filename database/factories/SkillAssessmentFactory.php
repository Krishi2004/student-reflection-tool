<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkillAssessment>
 */
class SkillAssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array // used to create fake data in the DB
    {
        return [
            'verification_token' => Str::random(32),
            'verifier_score' => null,
            'is_verified' => false,
        ];
    }
}
