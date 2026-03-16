<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Folder;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $folder = Folder::query()
            ->whereBelongsTo($request->user())
            ->findOrFail($data['folder_id']);

        $request->user()->tasks()->create([
            ...$data,
            'folder_id' => $folder->id,
        ]);

        return back()->with('status', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $data = $request->validated();

        $folder = Folder::query()
            ->whereBelongsTo($request->user())
            ->findOrFail($data['folder_id']);

        $task->update([
            ...$data,
            'folder_id' => $folder->id,
        ]);

        return back()->with('status', 'Task updated successfully.');
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('toggle', $task);

        $isCompleted = ! $task->is_completed;

        $task->update([
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return back()->with(
            'status',
            $isCompleted ? 'Task marked as completed.' : 'Task marked as active.'
        );
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return back()->with('status', 'Task deleted successfully.');
    }
}
