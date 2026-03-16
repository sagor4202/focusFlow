<div x-data="{ createFolderOpen: @js(old('_form') === 'folder-create') }" class="flex h-full flex-col">
    <div class="border-b border-slate-200/80 px-6 py-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/20">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h3.16c.6 0 1.17.24 1.59.66l1.34 1.34c.42.42.99.66 1.58.66H18A2.25 2.25 0 0 1 20.25 10v7.5A2.25 2.25 0 0 1 18 19.75H6A2.25 2.25 0 0 1 3.75 17.5v-10Z" />
                </svg>
            </span>

            <div>
                <p class="text-xl font-extrabold tracking-tight text-slate-900">{{ config('app.name', 'FocusFlow') }}</p>
                <p class="text-sm text-slate-500">
                    {{ $canManageTeam ? 'Organize every list in one calm workspace.' : 'Your assigned work inside the team workspace.' }}
                </p>
            </div>
        </a>
    </div>

    <div class="flex-1 space-y-8 overflow-y-auto px-4 py-6">
        <section class="space-y-3">
            <p class="focusflow-sidebar-label px-2">Smart Views</p>

            @foreach ($sidebarViews as $view)
                <a
                    href="{{ $view['key'] === 'all' ? route('dashboard') : route('views.show', $view['key']) }}"
                    class="focusflow-nav-link {{ $view['is_active'] ? 'focusflow-nav-link-active' : '' }}"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $view['is_active'] ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500' }}">
                        @switch($view['key'])
                            @case('today')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v2.5m7.5-2.5v2.5M4.5 9.25h15M6.75 5.75h10.5A2.25 2.25 0 0 1 19.5 8v10.25a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18.25V8a2.25 2.25 0 0 1 2.25-2.25Z" />
                                </svg>
                                @break
                            @case('important')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.62 5.31 5.86.85-4.24 4.13 1 5.84L12 16.36l-5.24 2.77 1-5.84-4.24-4.13 5.86-.85L12 3Z" />
                                </svg>
                                @break
                            @case('completed')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                @break
                            @default
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.75h5.5v5.5h-5.5v-5.5Zm9 0h5.5v5.5h-5.5v-5.5Zm-9 9h5.5v5.5h-5.5v-5.5Zm9 0h5.5v5.5h-5.5v-5.5Z" />
                                </svg>
                        @endswitch
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="truncate">{{ $view['label'] }}</p>
                        <p class="truncate text-xs {{ $view['is_active'] ? 'text-white/70' : 'text-slate-400' }}">{{ $view['description'] }}</p>
                    </div>

                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $view['is_active'] ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $view['count'] }}
                    </span>
                </a>
            @endforeach
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-2">
                <p class="focusflow-sidebar-label">Folders</p>

                @if ($canManageFolders)
                    <button
                        type="button"
                        @click="createFolderOpen = true"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 transition hover:bg-slate-900 hover:text-white"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                @endif
            </div>

            @if ($sidebarFolders->isEmpty())
                <div class="rounded-[24px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                    Create your first folder to start collecting tasks.
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($sidebarFolders as $folder)
                        <a
                            href="{{ route('folders.show', $folder) }}"
                            class="focusflow-nav-link {{ $selectedFolder?->is($folder) ? 'focusflow-nav-link-active' : '' }}"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $selectedFolder?->is($folder) ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h3.16c.6 0 1.17.24 1.59.66l1.34 1.34c.42.42.99.66 1.58.66H18A2.25 2.25 0 0 1 20.25 10v7.5A2.25 2.25 0 0 1 18 19.75H6A2.25 2.25 0 0 1 3.75 17.5v-10Z" />
                                </svg>
                            </span>

                            <span class="min-w-0 flex-1 truncate">{{ $folder->name }}</span>

                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $selectedFolder?->is($folder) ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $folder->tasks_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="border-t border-slate-200/80 p-4">
        <div class="rounded-[24px] bg-slate-50 px-4 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-slate-700 shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-500">
                        {{ $canManageTeam ? 'Team Leader' : 'Member of '.$workspaceOwner->name }}
                    </p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <a href="{{ route('profile.edit') }}" class="focusflow-secondary-button !py-2.5 !text-xs">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="focusflow-primary-button !w-full !py-2.5 !text-xs">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if ($canManageFolders)
        <div
            x-cloak
            x-show="createFolderOpen"
            class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
            role="dialog"
            aria-modal="true"
        >
            <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="createFolderOpen = false"></div>

            <div @click.away="createFolderOpen = false" class="focusflow-panel relative w-full max-w-md p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">New Folder</p>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Create a list</h2>
                        <p class="mt-2 text-sm text-slate-500">Start a new space for work, study, clients, or anything else.</p>
                    </div>

                    <button type="button" class="focusflow-icon-button h-10 w-10 rounded-xl" @click="createFolderOpen = false">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('folders.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="folder-create">

                    <div>
                        <label for="new-folder-name" class="mb-2 block text-sm font-semibold text-slate-700">Folder name</label>
                        <input
                            id="new-folder-name"
                            name="name"
                            type="text"
                            value="{{ old('_form') === 'folder-create' ? old('name') : '' }}"
                            class="focusflow-input"
                            placeholder="e.g. Work, Study, Personal"
                            required
                        >

                        @if (old('_form') === 'folder-create')
                            @error('name')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <button type="submit" class="focusflow-primary-button !w-full">
                        Create Folder
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
