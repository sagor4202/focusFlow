<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FocusFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_dashboard_shows_workspace_tasks_in_priority_order(): void
    {
        $leader = User::factory()->create();
        $member = User::factory()->member($leader)->create();
        $otherLeader = User::factory()->create();

        $folder = Folder::factory()->for($leader)->create(['name' => 'Work']);
        $otherFolder = Folder::factory()->for($otherLeader)->create(['name' => 'Private']);

        Task::factory()->for($leader)->for($folder)->create([
            'assigned_to_user_id' => $member->id,
            'title' => 'Priority 3 task',
            'priority' => 3,
            'created_at' => now()->subMinute(),
        ]);

        Task::factory()->for($leader)->for($folder)->create([
            'assigned_to_user_id' => $leader->id,
            'title' => 'Priority 0 newest',
            'priority' => 0,
            'created_at' => now(),
        ]);

        Task::factory()->for($leader)->for($folder)->create([
            'assigned_to_user_id' => $member->id,
            'title' => 'Priority 0 older',
            'priority' => 0,
            'created_at' => now()->subHour(),
        ]);

        Task::factory()->for($otherLeader)->for($otherFolder)->create([
            'assigned_to_user_id' => $otherLeader->id,
            'title' => 'Other workspace task',
            'priority' => 0,
        ]);

        $response = $this->actingAs($leader)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Other workspace task');
        $response->assertSeeInOrder([
            'Priority 0 newest',
            'Priority 0 older',
            'Priority 3 task',
        ]);
    }

    public function test_leader_can_create_member_and_assign_a_task(): void
    {
        $leader = User::factory()->create();
        $folder = Folder::factory()->for($leader)->create(['name' => 'Study']);

        $memberResponse = $this->actingAs($leader)->post(route('team-members.store'), [
            '_form' => 'team-member-create',
            'name' => 'Team Member',
            'email' => 'member@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $memberResponse->assertSessionHasNoErrors();

        $member = User::query()->where('email', 'member@example.com')->firstOrFail();

        $taskResponse = $this->actingAs($leader)->post(route('tasks.store'), [
            '_form' => 'task-create',
            'folder_id' => $folder->id,
            'title' => 'Review Laravel validation',
            'description' => 'Check form request rules and unique constraints.',
            'priority' => 1,
            'assigned_to_user_id' => $member->id,
            'due_date' => now()->toDateString(),
        ]);

        $taskResponse->assertSessionHasNoErrors();

        $task = Task::query()->firstOrFail();

        $this->assertSame($leader->id, $task->user_id);
        $this->assertSame($member->id, $task->assigned_to_user_id);
        $this->assertSame($folder->id, $task->folder_id);
    }

    public function test_member_only_sees_tasks_assigned_to_them(): void
    {
        $leader = User::factory()->create();
        $member = User::factory()->member($leader)->create();
        $otherMember = User::factory()->member($leader)->create();
        $folder = Folder::factory()->for($leader)->create(['name' => 'Clients']);

        Task::factory()->for($leader)->for($folder)->create([
            'assigned_to_user_id' => $member->id,
            'title' => 'Assigned to me',
            'priority' => 1,
        ]);

        Task::factory()->for($leader)->for($folder)->create([
            'assigned_to_user_id' => $otherMember->id,
            'title' => 'Assigned to someone else',
            'priority' => 0,
        ]);

        $response = $this->actingAs($member)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Assigned to me');
        $response->assertDontSee('Assigned to someone else');
        $response->assertDontSee('Add Task');
    }

    public function test_users_cannot_open_someone_elses_team_folder(): void
    {
        $leader = User::factory()->create();
        $otherLeader = User::factory()->create();
        $folder = Folder::factory()->for($otherLeader)->create();

        $response = $this->actingAs($leader)->get(route('folders.show', $folder));

        $response->assertForbidden();
    }
}
