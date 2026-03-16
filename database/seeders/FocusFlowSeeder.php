<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FocusFlowSeeder extends Seeder
{
    public function run(): void
    {
        $leader = User::query()->updateOrCreate(
            ['email' => 'demo@focusflow.test'],
            [
                'name' => 'FocusFlow Lead',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_LEADER,
                'leader_id' => null,
            ]
        );

        $designer = User::query()->updateOrCreate(
            ['email' => 'sadia@focusflow.test'],
            [
                'name' => 'Sadia Rahman',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_MEMBER,
                'leader_id' => $leader->id,
            ]
        );

        $developer = User::query()->updateOrCreate(
            ['email' => 'rafi@focusflow.test'],
            [
                'name' => 'Rafi Hasan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_MEMBER,
                'leader_id' => $leader->id,
            ]
        );

        $privateUser = User::query()->updateOrCreate(
            ['email' => 'private@focusflow.test'],
            [
                'name' => 'Private User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_LEADER,
                'leader_id' => null,
            ]
        );

        Task::query()->whereBelongsTo($leader)->delete();
        Folder::query()->whereBelongsTo($leader)->delete();
        Task::query()->whereBelongsTo($privateUser)->delete();
        Folder::query()->whereBelongsTo($privateUser)->delete();

        $folders = collect([
            'Work',
            'Study',
            'Personal',
            'Clients',
        ])->mapWithKeys(function (string $name) use ($leader) {
            $folder = $leader->folders()->create(['name' => $name]);

            return [$name => $folder];
        });

        $assignees = [
            'lead' => $leader,
            'designer' => $designer,
            'developer' => $developer,
        ];

        $tasks = [
            [
                'folder' => 'Work',
                'title' => 'Prepare board meeting deck',
                'description' => 'Finalize the quarterly goals section and attach the sales snapshot.',
                'priority' => 0,
                'due_date' => today(),
                'is_completed' => false,
                'assignee' => 'lead',
            ],
            [
                'folder' => 'Work',
                'title' => 'Review contract revisions',
                'description' => 'Check the updated terms from legal before sending them to procurement.',
                'priority' => 1,
                'due_date' => today()->addDay(),
                'is_completed' => false,
                'assignee' => 'developer',
            ],
            [
                'folder' => 'Work',
                'title' => 'Follow up on Q2 invoice approvals',
                'description' => 'Confirm the remaining approvals with finance.',
                'priority' => 2,
                'due_date' => null,
                'is_completed' => true,
                'completed_at' => now()->subDay(),
                'assignee' => 'lead',
            ],
            [
                'folder' => 'Study',
                'title' => 'Finish Laravel policies lesson',
                'description' => 'Write notes on ownership rules and scoped authorization checks.',
                'priority' => 0,
                'due_date' => today(),
                'is_completed' => true,
                'completed_at' => now()->subHours(5),
                'assignee' => 'designer',
            ],
            [
                'folder' => 'Study',
                'title' => 'Draft database normalization notes',
                'description' => 'Summarize 1NF through 3NF with examples for the next revision session.',
                'priority' => 1,
                'due_date' => today()->addDays(2),
                'is_completed' => false,
                'assignee' => 'designer',
            ],
            [
                'folder' => 'Study',
                'title' => 'Practice Blade component patterns',
                'description' => 'Refactor repeated UI blocks into small reusable view pieces.',
                'priority' => 2,
                'due_date' => null,
                'is_completed' => false,
                'assignee' => 'developer',
            ],
            [
                'folder' => 'Personal',
                'title' => 'Book annual health checkup',
                'description' => 'Call the clinic and confirm an evening appointment slot.',
                'priority' => 1,
                'due_date' => today(),
                'is_completed' => false,
                'assignee' => 'lead',
            ],
            [
                'folder' => 'Personal',
                'title' => 'Plan family trip budget',
                'description' => 'Estimate flights, stays, and transport for the long weekend.',
                'priority' => 3,
                'due_date' => null,
                'is_completed' => false,
                'assignee' => 'designer',
            ],
            [
                'folder' => 'Clients',
                'title' => 'Send onboarding checklist to Aster Labs',
                'description' => 'Include shared drive links and the delivery timeline.',
                'priority' => 0,
                'due_date' => today(),
                'is_completed' => false,
                'assignee' => 'developer',
            ],
            [
                'folder' => 'Clients',
                'title' => 'Update April sprint scope',
                'description' => 'Incorporate the revised milestone dates from the kickoff call.',
                'priority' => 2,
                'due_date' => today()->addDay(),
                'is_completed' => true,
                'completed_at' => now()->subHours(12),
                'assignee' => 'developer',
            ],
        ];

        foreach ($tasks as $index => $task) {
            $leader->tasks()->create([
                'folder_id' => $folders[$task['folder']]->id,
                'assigned_to_user_id' => $assignees[$task['assignee']]->id,
                'title' => $task['title'],
                'description' => $task['description'],
                'priority' => $task['priority'],
                'is_completed' => $task['is_completed'],
                'completed_at' => $task['completed_at'] ?? null,
                'due_date' => $task['due_date'],
                'created_at' => now()->subHours($index * 3),
                'updated_at' => now()->subHours($index * 3),
            ]);
        }

        $privateFolder = $privateUser->folders()->create([
            'name' => 'Private List',
        ]);

        $privateUser->tasks()->create([
            'folder_id' => $privateFolder->id,
            'assigned_to_user_id' => $privateUser->id,
            'title' => 'This task belongs to another account',
            'description' => 'Use this to verify users only see their own team workspace.',
            'priority' => 0,
            'is_completed' => false,
            'due_date' => today()->addDays(3),
        ]);
    }
}
