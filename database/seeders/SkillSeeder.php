<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserting the core skills list
        DB::table('skills')->insert([
            [
                'name' => 'Leadership', 
                'description' => 'Ability to guide teams, make decisions, and take initiative.'
            ],
            [
                'name' => 'Presenting', 
                'description' => 'Ability to deliver clear, engaging information to an audience with confidence.'
            ],
            [
                'name' => 'Teamwork', 
                'description' => 'Ability to collaborate effectively, manage conflicts, and listen to peers.'
            ],
            [
                'name' => 'Problem-Solving', 
                'description' => 'Ability to diagnose complex issues, evaluate options, and formulate effective solutions.'
            ],
            [
                'name' => 'Communication', 
                'description' => 'Ability to convey messages clearly (verbal and written) and actively listen.'
            ],
            [
                'name' => 'Time Management', 
                'description' => 'Ability to prioritize tasks, meet deadlines, and work efficiently under pressure.'
            ],
        ]);
    }
}
