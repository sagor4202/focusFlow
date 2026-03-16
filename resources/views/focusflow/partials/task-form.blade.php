@if (! $canManageTasks)
    <section class="focusflow-panel p-6 sm:p-7">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Assigned Workflow</p>
                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Your leader assigns the work</h2>
                <p class="mt-2 max-w-xl text-sm text-slate-500">
                    {{ $workspaceOwner->name }} manages the team lists and task assignments. You can open your tasks here and update their completion status.
                </p>
            </div>
        </div>
    </section>
@elseif ($sidebarFolders->isEmpty())
    <section class="focusflow-panel p-6 sm:p-7">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Ready to Start</p>
                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Create a folder first</h2>
                <p class="mt-2 max-w-xl text-sm text-slate-500">
                    Tasks live inside folders in FocusFlow. Add your first folder from the sidebar, then come back here to start planning.
                </p>
            </div>
        </div>
    </section>
@else
    <section class="focusflow-panel p-5 sm:p-6 lg:p-7">
        <div x-data="{ showDetails: @js(old('_form') === 'task-create' && (old('description') || old('due_date'))) }" class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Quick Add</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Capture a new task</h2>
                </div>

                <button type="button" @click="showDetails = !showDetails" class="focusflow-secondary-button">
                    <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    More options
                </button>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_form" value="task-create">

                <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_220px_220px_220px_auto]">
                    <div>
                        <label for="task-title" class="sr-only">Task title</label>
                        <input
                            id="task-title"
                            name="title"
                            type="text"
                            value="{{ old('_form') === 'task-create' ? old('title') : '' }}"
                            class="focusflow-input"
                            placeholder="Add a task..."
                            required
                        >

                        @if (old('_form') === 'task-create')
                            @error('title')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="task-folder" class="sr-only">Folder</label>
                        <select id="task-folder" name="folder_id" class="focusflow-select" required>
                            @foreach ($sidebarFolders as $folder)
                                <option
                                    value="{{ $folder->id }}"
                                    @selected((string) (old('_form') === 'task-create' ? old('folder_id') : $defaultFolderId) === (string) $folder->id)
                                >
                                    {{ $folder->name }}
                                </option>
                            @endforeach
                        </select>

                        @if (old('_form') === 'task-create')
                            @error('folder_id')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="task-priority" class="sr-only">Priority</label>
                        <select id="task-priority" name="priority" class="focusflow-select" required>
                            @foreach ($taskPriorityOptions as $option)
                                <option
                                    value="{{ $option['value'] }}"
                                    @selected((string) (old('_form') === 'task-create' ? old('priority', 2) : 2) === (string) $option['value'])
                                >
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>

                        @if (old('_form') === 'task-create')
                            @error('priority')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="task-assignee" class="sr-only">Assignee</label>
                        <select id="task-assignee" name="assigned_to_user_id" class="focusflow-select" required>
                            @foreach ($assigneeOptions as $assignee)
                                <option
                                    value="{{ $assignee['id'] }}"
                                    @selected((string) (old('_form') === 'task-create' ? old('assigned_to_user_id', auth()->id()) : auth()->id()) === (string) $assignee['id'])
                                >
                                    {{ $assignee['name'] }} ({{ $assignee['role'] }})
                                </option>
                            @endforeach
                        </select>

                        @if (old('_form') === 'task-create')
                            @error('assigned_to_user_id')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <button type="submit" class="focusflow-primary-button !h-full !px-6">
                        Add Task
                    </button>
                </div>

                <div x-cloak x-show="showDetails" class="grid gap-4 border-t border-slate-200/80 pt-4 md:grid-cols-[minmax(0,1fr)_220px]">
                    <div>
                        <label for="task-description" class="mb-2 block text-sm font-semibold text-slate-700">Notes</label>
                        <textarea
                            id="task-description"
                            name="description"
                            class="focusflow-textarea"
                            placeholder="Optional notes, context, or next steps..."
                        >{{ old('_form') === 'task-create' ? old('description') : '' }}</textarea>

                        @if (old('_form') === 'task-create')
                            @error('description')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="task-due-date" class="mb-2 block text-sm font-semibold text-slate-700">Due date</label>
                        <input
                            id="task-due-date"
                            name="due_date"
                            type="date"
                            value="{{ old('_form') === 'task-create' ? old('due_date') : '' }}"
                            class="focusflow-input"
                        >

                        @if (old('_form') === 'task-create')
                            @error('due_date')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>
@endif
