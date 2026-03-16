<section class="focusflow-panel p-5 sm:p-6 lg:p-7">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.28em] text-sky-600">Team Management</p>
            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Create members and assign work</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Add your team members here, then assign tasks directly from the FocusFlow board.
            </p>
        </div>

        <div class="rounded-[22px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">
            {{ $teamMembers->count() }} member{{ $teamMembers->count() === 1 ? '' : 's' }}
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <form method="POST" action="{{ route('team-members.store') }}" class="space-y-4 rounded-[26px] border border-slate-200 bg-slate-50 p-5">
            @csrf
            <input type="hidden" name="_form" value="team-member-create">

            <div>
                <label for="member-name" class="mb-2 block text-sm font-semibold text-slate-700">Member name</label>
                <input
                    id="member-name"
                    name="name"
                    type="text"
                    value="{{ old('_form') === 'team-member-create' ? old('name') : '' }}"
                    class="focusflow-input"
                    placeholder="e.g. Sadia Rahman"
                    required
                >

                @if (old('_form') === 'team-member-create')
                    @error('name')
                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div>
                <label for="member-email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input
                    id="member-email"
                    name="email"
                    type="email"
                    value="{{ old('_form') === 'team-member-create' ? old('email') : '' }}"
                    class="focusflow-input"
                    placeholder="member@focusflow.test"
                    required
                >

                @if (old('_form') === 'team-member-create')
                    @error('email')
                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-1">
                <div>
                    <label for="member-password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input
                        id="member-password"
                        name="password"
                        type="password"
                        class="focusflow-input"
                        required
                    >

                    @if (old('_form') === 'team-member-create')
                        @error('password')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div>
                    <label for="member-password-confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</label>
                    <input
                        id="member-password-confirmation"
                        name="password_confirmation"
                        type="password"
                        class="focusflow-input"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="focusflow-primary-button !w-full">
                Add Team Member
            </button>
        </form>

        <div class="space-y-3">
            @forelse ($teamMembers as $member)
                <div class="rounded-[26px] border border-slate-200 bg-white/80 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-lg font-bold tracking-tight text-slate-900">{{ $member->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $member->email }}</p>
                        </div>

                        <div class="flex flex-wrap gap-3 text-sm">
                            <span class="focusflow-badge bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                {{ $member->assigned_tasks_count }} assigned
                            </span>
                            <span class="focusflow-badge bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                {{ $member->completed_tasks_count }} completed
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-[26px] border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
                    <h3 class="text-xl font-bold tracking-tight text-slate-900">No team members yet</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Create your first member account to start assigning tasks.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</section>
