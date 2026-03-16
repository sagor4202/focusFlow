<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FocusFlowBoardService
{
    protected array $smartViews = [
        'all' => [
            'label' => 'All Tasks',
            'description' => 'Everything that is visible inside your team workspace.',
        ],
        'today' => [
            'label' => 'Today',
            'description' => 'Tasks with a due date scheduled for today.',
        ],
        'important' => [
            'label' => 'Important',
            'description' => 'Priority 0 tasks surfaced first for the team.',
        ],
        'completed' => [
            'label' => 'Completed',
            'description' => 'Finished work across the current workspace.',
        ],
    ];

    public function hasSmartView(string $view): bool
    {
        return array_key_exists($view, $this->smartViews);
    }

    public function buildSmartView(User $user, string $view): array
    {
        $workspaceOwner = $user->workspaceOwner();
        $view = $this->hasSmartView($view) ? $view : 'all';
        $query = $this->applySmartView($this->baseTaskQuery($user, $workspaceOwner), $view);

        return $this->makePayload(
            user: $user,
            workspaceOwner: $workspaceOwner,
            taskQuery: $query,
            pageTitle: $this->smartViews[$view]['label'],
            pageDescription: $this->smartViews[$view]['description'],
            activeSmartView: $view,
        );
    }

    public function buildFolder(User $user, Folder $folder): array
    {
        $workspaceOwner = $user->workspaceOwner();
        $query = $this->baseTaskQuery($user, $workspaceOwner)->whereBelongsTo($folder);

        return $this->makePayload(
            user: $user,
            workspaceOwner: $workspaceOwner,
            taskQuery: $query,
            pageTitle: $folder->name,
            pageDescription: 'A focused list of tasks grouped inside this folder.',
            activeSmartView: null,
            selectedFolder: $folder,
        );
    }

    protected function makePayload(
        User $user,
        User $workspaceOwner,
        Builder $taskQuery,
        string $pageTitle,
        string $pageDescription,
        ?string $activeSmartView = null,
        ?Folder $selectedFolder = null,
    ): array {
        $tasks = $taskQuery->get();
        $folders = $workspaceOwner->folders()
            ->withCount([
                'tasks' => fn (Builder $query) => $user->isLeader()
                    ? $query
                    : $query->where('assigned_to_user_id', $user->id),
            ])
            ->orderBy('name')
            ->get();

        return [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'currentDate' => now(),
            'tasks' => $tasks,
            'stats' => [
                'total' => $tasks->count(),
                'pending' => $tasks->where('is_completed', false)->count(),
                'completed' => $tasks->where('is_completed', true)->count(),
            ],
            'sidebarViews' => $this->buildSmartViewItems($user, $workspaceOwner, $activeSmartView),
            'sidebarFolders' => $folders,
            'selectedFolder' => $selectedFolder,
            'activeSmartView' => $activeSmartView,
            'taskPriorityOptions' => Task::priorityOptions(),
            'defaultFolderId' => $selectedFolder?->id ?? $folders->first()?->id,
            'assigneeOptions' => $this->buildAssigneeOptions($workspaceOwner),
            'teamMembers' => $this->buildTeamMembers($workspaceOwner),
            'canManageTeam' => $user->isLeader(),
            'canManageFolders' => $user->isLeader(),
            'canManageTasks' => $user->isLeader(),
            'workspaceOwner' => $workspaceOwner,
        ];
    }

    protected function buildSmartViewItems(User $user, User $workspaceOwner, ?string $activeSmartView): Collection
    {
        $baseQuery = Task::query()->where('user_id', $workspaceOwner->id);

        if ($user->isMember()) {
            $baseQuery->where('assigned_to_user_id', $user->id);
        }

        return collect($this->smartViews)
            ->map(function (array $view, string $key) use ($baseQuery, $activeSmartView) {
                $count = $this->applySmartView(clone $baseQuery, $key)->count();

                return [
                    'key' => $key,
                    'label' => $view['label'],
                    'description' => $view['description'],
                    'count' => $count,
                    'is_active' => $activeSmartView === $key,
                ];
            })
            ->values();
    }

    protected function baseTaskQuery(User $user, User $workspaceOwner): Builder
    {
        return Task::query()
            ->where('user_id', $workspaceOwner->id)
            ->when(
                $user->isMember(),
                fn (Builder $query) => $query->where('assigned_to_user_id', $user->id)
            )
            ->with(['folder', 'assignee'])
            ->ordered();
    }

    protected function buildAssigneeOptions(User $workspaceOwner): Collection
    {
        return collect([$workspaceOwner])
            ->merge($workspaceOwner->members()->orderBy('name')->get())
            ->map(fn (User $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'role' => $member->isLeader() ? 'Leader' : 'Member',
            ])
            ->values();
    }

    protected function buildTeamMembers(User $workspaceOwner): Collection
    {
        return $workspaceOwner->members()
            ->withCount([
                'assignedTasks',
                'assignedTasks as completed_tasks_count' => fn (Builder $query) => $query->where('is_completed', true),
            ])
            ->orderBy('name')
            ->get();
    }

    protected function applySmartView(Builder $query, string $view): Builder
    {
        return match ($view) {
            'today' => $query->dueToday(),
            'important' => $query->important(),
            'completed' => $query->completed(),
            default => $query,
        };
    }
}
