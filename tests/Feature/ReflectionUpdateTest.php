<?php

namespace Tests\Feature;

use App\Models\SkillAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Skill;
use App\Models\Reflection;
use App\Models\User;

class ReflectionUpdateTest extends TestCase
{
    use RefreshDatabase;
    public function test_expert_score_save_without_action_plan()
    {

        $this->seed(\Database\Seeders\SkillSeeder::class);

        // Grab the very first skill from that seeded list
        $skill = Skill::first();

        // Create a fake user and a fake reflection to edit
        $user = User::factory()->create();
        $reflection = Reflection::factory()->create([
            'user_id' => $user->id,
            'r_quality_score' => 2.0, // Starting with a low score
        ]);

        $response = $this->actingAs($user)->put(route('reflection.update', $reflection->id), [
            'title' => 'My Updated Reflection',
            'skill_id' => $skill->id,
            'self_score' => 5, // A perfect score means no action plan should be saved
            'supervisor_email' => 'boss@example.com',
            'situation' => 'This is a test situation with more than twenty characters to pass validation.',
            'task' => 'This is a test task with more than twenty characters to pass validation.',
            'action' => 'This is a test action with more than twenty characters to pass validation.',
            'result' => 'This is a test result with more than twenty characters to pass validation.',
            'analysis' => 'This is a test analysis with more than twenty characters to pass validation.',
        ]);

        // Check if the system behaved correctly
        
        $response->assertRedirect(route('reflection'));

        
        $this->assertDatabaseHas('reflections', [ // checks if it updates the DB
            'id' => $reflection->id,
            'title' => 'My Updated Reflection',
        ]);

        // Did the system successfully ignore/remove the action plan because the score was 5?
        $freshReflection = Reflection::find($reflection->id);
        $this->assertNull($freshReflection->narrative['action_plan']);
    }


    public function test_system_generates_hashtoken_on_submission() 
    {

        $this->seed(\Database\Seeders\SkillSeeder::class);


        $skill = Skill::first();


        $user = User::factory()->create(); // creates a fake user
        $reflection = Reflection::factory()->create([ // creates a fake reflection with the above user and quality score of 4
            'user_id' => $user->id,
            'r_quality_score' => 4.0,
        ]);

        $response = $this->actingAs($user)->post(route('reflections.store', $reflection->id), [
            'title' => 'My Updated Reflection',
            'skill_id' => $skill->id,
            'self_score' => 5, // A perfect score means no action plan should be saved
            'supervisor_email' => 'boss@example.com',
            'situation' => 'This is a test situation with more than twenty characters to pass validation.',
            'task' => 'This is a test task with more than twenty characters to pass validation.',
            'action' => 'This is a test action with more than twenty characters to pass validation.',
            'result' => 'This is a test result with more than twenty characters to pass validation.',
            'analysis' => 'This is a test analysis with more than twenty characters to pass validation.',
        ]);

        $response->assertSessionHasNoErrors();

        $reflection = Reflection::latest()->first();
        $assessment = SkillAssessment::latest()->first();


        $this->assertNotNull($assessment, 'The skill assessment row was not created.'); // checks for a record in the skillAssessment DB


        $this->assertNotNull($assessment->verification_token, 'The SHA-256 hash was not saved.'); // checks if the has is saved in the DB


        $this->assertEquals(64, strlen($assessment->verification_token), 'The token is not 64 characters long.'); // checks if the token is encrypted using regex
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{64}$/i',
            $assessment->verification_token,
            'The token contains invalid characters for a SHA-256 hash.'
        );

    }

    public function test_user_cannot_edit_someone_elses_reflection()
    {

        $this->seed(\Database\Seeders\SkillSeeder::class);

        $skill = Skill::first(); // selects the first skill from the above 


        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $reflection = Reflection::factory()->create([
            'user_id' => $user1->id,

        ]);

        $response = $this->actingAs($user2)->put(route('reflection.update', $reflection->id), [
            'title' => 'My Updated Reflection',
            'skill_id' => $skill->id,
            'self_score' => 5,
            'supervisor_email' => 'boss@example.com',
            'situation' => 'This is a test situation with more than twenty characters to pass validation.',
            'task' => 'This is a test task with more than twenty characters to pass validation.',
            'action' => 'This is a test action with more than twenty characters to pass validation.',
            'result' => 'This is a test result with more than twenty characters to pass validation.',
            'analysis' => 'This is a test analysis with more than twenty characters to pass validation.',
        ]);

        $response->assertStatus(403); // unauthorised access error

    }

    public function test_reflection_requires_minimum_characters_in_length()
    {
        $this->seed(\Database\Seeders\SkillSeeder::class);

        $skill = Skill::first();


        $user = User::factory()->create();
        $reflection = Reflection::factory()->create([
            'user_id' => $user->id,
            'r_quality_score' => 4.0,
        ]);

        $response = $this->actingAs($user)->put(route('reflection.update', $reflection->id), [
            'title' => 'My Updated Reflection',
            'skill_id' => $skill->id,
            'self_score' => 5,
            'supervisor_email' => 'boss@example.com',
            'situation' => 'test',
            'task' => 'task',
            'action' => 'This is long enough though',
            'result' => 'This is long enough though',
            'analysis' => 'This is long enough though',
        ]);

        $response->assertSessionHasErrors(['situation', 'task']); // checks minimum character errors
    }

    public function test_supervisor_review_page_loads_correctly()
    {

        $this->seed(\Database\Seeders\SkillSeeder::class);

        $skill = Skill::first();


        $user = User::factory()->create();
        $reflection = Reflection::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('reflection.review', $reflection->id));

        $response->assertStatus(200);
        $response->assertViewIs('supervisor-review');
        $response->assertSee($reflection->title);
    }

    public function test_token_cannot_be_used_twice(){

        $reflection = Reflection::factory()->create();
        $skill = Skill::factory()->create();

        $assessment = SkillAssessment::factory()->create([
            'verification_token' => 'thisisatesttoken12345',
            'verifier_score' => null,
            'reflection_id' => $reflection->id,
            'skill_id' => $skill->id,
            'self_score' => 4,
        ]);

        

        $response1 = $this->post("/verify-reflection/{$reflection->id}", [
            'verifier_score' => 4,
            'verification_token' => $assessment->verification_token,

        ]);

        $response1->assertStatus(200);

        $reponse2 = $this->post("/verify-reflection/{$reflection->id}", [
            'verifier_score' => 5,
            'verification_token' => $assessment->verification_token,
        ]);

        $reponse2->assertStatus(403);

    }

}
