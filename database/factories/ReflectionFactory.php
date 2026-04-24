<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reflection>
 */
class ReflectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array // used for the unit test to populate a fake DB
    {
        return [
            
            'user_id' => User::factory(), // create a fake user
            'title' => fake()->sentence(),
            'template_used' => 'STAR',
            'r_quality_score' => fake()->randomFloat(1, 1, 5), // Random score between 1 and 5
            'narrative' => [
                'situation' => fake()->text(100),
                'task'      => fake()->text(100),
                'action'    => fake()->text(100),
                'result'    => fake()->text(100),
                'analysis'  => fake()->text(100),
                'action_plan' => [fake()->sentence(), fake()->sentence()]
            ],
    
        ];
    }
}
