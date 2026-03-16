<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamMemberRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class TeamMemberController extends Controller
{
    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $member = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => User::ROLE_MEMBER,
            'leader_id' => $request->user()->id,
            'email_verified_at' => now(),
        ]);

        return back()->with('status', sprintf('%s was added to your team.', $member->name));
    }
}
