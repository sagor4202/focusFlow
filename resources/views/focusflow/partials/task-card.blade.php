@php
    $priorityStyles = [
        0 => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
        1 => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        2 => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
        3 => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
    ];
    $searchableText = strtolower(trim($task->title.' '.($task->description ?? '').' '.$task->folder->name));
    $isOverdue = $task->due_date && ! $task->is_completed && $task->due_date->isPast() && ! $task->due_date->isToday();
    $editFormKey = 'task-update-'.$task->id;
@endphp

<article
    x-data="{ editOpen: @js(old('_form') === $editFormKey) }"
    x-show="matchesTask(@js($searchableText), {{ $task->is_completed ? 'true' : 'false' }})"
    x-transition.opacity.duration.150ms
    class="focusflow-task-card {{ $task->is_completed ? 'focusflow-task-card-completed' : '' }}"
>
    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
        <div class="flex items-start gap-4">
            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-1">
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="focusflow-task-toggle {{ $task->is_completed ? 'focusflow-task-toggle-complete' : '' }}"
                    aria-label="{{ $task->is_completed ? 'Mark as incomplete' : 'Mark as completed' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                    </svg>
                </button>
            </form>

            <div class="space-y-3">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-bold tracking-tight {{ $task->is_completed ? 'line-through decoration-2' : 'text-slate-900' }}">
                            {{ $task->title }}
                        </h3>

                        <span class="focusflow-badge {{ $priorityStyles[$task->priority] ?? $priorityStyles[2] }}">
                            {{ $task->priorityLabel() }}
                        </span>

                        @if ($task->assignee)
                            <span class="focusflow-badge bg-violet-50 text-violet-700 ring-1 ring-violet-200">
                                {{ $task->assignee->name }}
                            </span>
                        @endif

                        @if (! $selectedFolder)
                            <span class="focusflow-badge bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                {{ $task->folder->name }}
                            </span>
                        @endif
                    </div>

                    @if ($task->description)
                        <p class="max-w-3xl text-sm leading-6 text-slate-500">
                            {{ $task->description }}
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    @if ($task->due_date)
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 font-medium {{ $isOverdue ? 'bg-rose-50 text-rose-700' : ($task->due_date->isToday() ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v2.5m7.5-2.5v2.5M4.5 9.25h15M6.75 5.75h10.5A2.25 2.25 0 0 1 19.5 8v10.25a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18.25V8a2.25 2.25 0 0 1 2.25-2.25Z" />
                            </svg>
                            {{ $task->due_date->isToday() ? 'Due today' : $task->due_date->format('M j, Y') }}
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Created {{ $task->created_at->format('M j, Y') }}
                    </span>

                    @if ($task->is_completed && $task->completed_at)
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 font-medium text-emerald-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                            Completed {{ $task->completed_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if ($canManageTasks)
            <div class="flex items-center gap-3">
                <button type="button" @click="editOpen = true" class="focusflow-secondary-button">
                    Edit
                </button>

                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?');">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="focusflow-secondary-button text-rose-600 hover:border-rose-200 hover:text-rose-700">
                        Delete
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if ($canManageTasks)
        <div
            x-cloak
            x-show="editOpen"
            class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
            role="dialog"
            aria-modal="true"
        >
            <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="editOpen = false"></div>

            <div @click.away="editOpen = false" class="focusflow-panel relative w-full max-w-2xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Edit Task</p>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Update task details</h2>
                        <p class="mt-2 text-sm text-slate-500">Keep priorities clear and move tasks between folders when plans change.</p>
                    </div>

                    <button type="button" class="focusflow-icon-button h-10 w-10 rounded-xl" @click="editOpen = false">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="{{ $editFormKey }}">

                    <div>
                        <label for="title-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Title</label>
                        <input
                            id="title-{{ $task->id }}"
                            name="title"
                            type="text"
                            value="{{ old('_form') === $editFormKey ? old('title') : $task->title }}"
                            class="focusflow-input"
                            required
                        >

                        @if (old('_form') === $editFormKey)
                            @error('title')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="folder-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Folder</label>
                            <select id="folder-{{ $task->id }}" name="folder_id" class="focusflow-select" required>
                                @foreach ($sidebarFolders as $folder)
                                    <option
                                        value="{{ $folder->id }}"
                                        @selected((string) (old('_form') === $editFormKey ? old('folder_id') : $task->folder_id) === (string) $folder->id)
                                    >
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>

                            @if (old('_form') === $editFormKey)
                                @error('folder_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label for="priority-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Priority</label>
                            <select id="priority-{{ $task->id }}" name="priority" class="focusflow-select" required>
                                @foreach ($taskPriorityOptions as $option)
                                    <option
                                        value="{{ $option['value'] }}"
                                        @selected((string) (old('_form') === $editFormKey ? old('priority') : $task->priority) === (string) $option['value'])
                                    >
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            @if (old('_form') === $editFormKey)
                                @error('priority')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label for="assignee-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Assignee</label>
                            <select id="assignee-{{ $task->id }}" name="assigned_to_user_id" class="focusflow-select" required>
                                @foreach ($assigneeOptions as $assignee)
                                    <option
                                        value="{{ $assignee['id'] }}"
                                        @selected((string) (old('_form') === $editFormKey ? old('assigned_to_user_id', $task->assigned_to_user_id) : $task->assigned_to_user_id) === (string) $assignee['id'])
                                    >
                                        {{ $assignee['name'] }} ({{ $assignee['role'] }})
                                    </option>
                                @endforeach
                            </select>

                            @if (old('_form') === $editFormKey)
                                @error('assigned_to_user_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px]">
                        <div>
                            <label for="description-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Notes</label>
                            <textarea
                                id="description-{{ $task->id }}"
                                name="description"
                                class="focusflow-textarea"
                                placeholder="Optional context or notes..."
                            >{{ old('_form') === $editFormKey ? old('description') : $task->description }}</textarea>

                            @if (old('_form') === $editFormKey)
                                @error('description')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>

                        <div>
                            <label for="due-date-{{ $task->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Due date</label>
                            <input
                                id="due-date-{{ $task->id }}"
                                name="due_date"
                                type="date"
                                value="{{ old('_form') === $editFormKey ? old('due_date') : optional($task->due_date)->format('Y-m-d') }}"
                                class="focusflow-input"
                            >

                            @if (old('_form') === $editFormKey)
                                @error('due_date')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="focusflow-secondary-button" @click="editOpen = false">
                            Cancel
                        </button>

                        <button type="submit" class="focusflow-primary-button">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</article>
