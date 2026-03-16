@extends('layouts.focusflow')

@section('content')
    <div x-data="focusFlowBoard()" class="space-y-6">
        <section class="focusflow-panel p-6 sm:p-7 lg:p-8">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.28em] text-sky-700">
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                        {{ $selectedFolder ? 'Folder View' : 'Smart View' }}
                    </span>

                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $pageTitle }}</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                            {{ $pageDescription }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">
                        {{ $canManageTeam ? 'Team Leader' : 'Team Member' }}
                    </div>

                    <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">
                        {{ $currentDate->format('l, F j') }}
                    </div>

                    @if ($selectedFolder && $canManageFolders)
                        @php($renameFolderKey = 'folder-update-'.$selectedFolder->id)

                        <div x-data="{ renameOpen: @js(old('_form') === $renameFolderKey) }" class="flex items-center gap-3">
                            <button type="button" @click="renameOpen = true" class="focusflow-secondary-button">
                                Rename Folder
                            </button>

                            <form method="POST" action="{{ route('folders.destroy', $selectedFolder) }}" onsubmit="return confirm('Delete this folder and all of its tasks?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="focusflow-secondary-button text-rose-600 hover:border-rose-200 hover:text-rose-700">
                                    Delete Folder
                                </button>
                            </form>

                            <div
                                x-cloak
                                x-show="renameOpen"
                                class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
                                role="dialog"
                                aria-modal="true"
                            >
                                <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="renameOpen = false"></div>

                                <div @click.away="renameOpen = false" class="focusflow-panel relative w-full max-w-md p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Rename Folder</p>
                                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">{{ $selectedFolder->name }}</h2>
                                            <p class="mt-2 text-sm text-slate-500">Update the list name without losing any tasks.</p>
                                        </div>

                                        <button type="button" class="focusflow-icon-button h-10 w-10 rounded-xl" @click="renameOpen = false">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                                            </svg>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('folders.update', $selectedFolder) }}" class="mt-6 space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_form" value="{{ $renameFolderKey }}">

                                        <div>
                                            <label for="folder-name-{{ $selectedFolder->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Folder name</label>
                                            <input
                                                id="folder-name-{{ $selectedFolder->id }}"
                                                name="name"
                                                type="text"
                                                value="{{ old('_form') === $renameFolderKey ? old('name') : $selectedFolder->name }}"
                                                class="focusflow-input"
                                                required
                                            >

                                            @if (old('_form') === $renameFolderKey)
                                                @error('name')
                                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                                @enderror
                                            @endif
                                        </div>

                                        <button type="submit" class="focusflow-primary-button !w-full">
                                            Save Folder
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-[22px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-[22px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Please fix the highlighted fields and try again.</p>
                </div>
            @endif
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="focusflow-panel p-5 sm:p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.75h5.5v5.5h-5.5v-5.5Zm9 0h5.5v5.5h-5.5v-5.5Zm-9 9h5.5v5.5h-5.5v-5.5Zm9 0h5.5v5.5h-5.5v-5.5Z" />
                        </svg>
                    </span>

                    <div>
                        <p class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $stats['total'] }}</p>
                        <p class="text-sm text-slate-500">Total tasks</p>
                    </div>
                </div>
            </div>

            <div class="focusflow-panel p-5 sm:p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>

                    <div>
                        <p class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-slate-500">Pending tasks</p>
                    </div>
                </div>
            </div>

            <div class="focusflow-panel p-5 sm:p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                        </svg>
                    </span>

                    <div>
                        <p class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $stats['completed'] }}</p>
                        <p class="text-sm text-slate-500">Completed tasks</p>
                    </div>
                </div>
            </div>
        </section>

        @if ($canManageTeam)
            @include('focusflow.partials.team-panel')
        @endif

        @include('focusflow.partials.task-form')

        <section class="focusflow-panel p-5 sm:p-6 lg:p-7">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Task Board</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Priority-first list</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Tasks are always sorted from priority 0 to 3. Items with the same priority stay newest-first.
                    </p>
                </div>

                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <label class="relative block min-w-[260px]">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </span>

                        <input
                            x-model="taskSearch"
                            type="search"
                            class="focusflow-input pl-12"
                            placeholder="Search tasks..."
                        >
                    </label>

                    <div class="flex items-center gap-2 rounded-[20px] bg-slate-100 p-1">
                        <button
                            type="button"
                            @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                            class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                        >
                            All
                        </button>

                        <button
                            type="button"
                            @click="statusFilter = 'active'"
                            :class="statusFilter === 'active' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                            class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                        >
                            Active
                        </button>

                        <button
                            type="button"
                            @click="statusFilter = 'completed'"
                            :class="statusFilter === 'completed' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                            class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
                        >
                            Done
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($tasks as $task)
                    @include('focusflow.partials.task-card', ['task' => $task])
                @empty
                    <div class="rounded-[26px] border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900">Nothing here yet</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ $selectedFolder ? 'Add a task to this folder to start building momentum.' : 'This view is empty right now. Add a task or switch to another list.' }}
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
