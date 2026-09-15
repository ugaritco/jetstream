<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Jetstream\Actions\UpdateTeamMemberRole;
use Ugarit\Jetstream\Contracts\AddsTeamMembers;
use Ugarit\Jetstream\Contracts\InvitesTeamMembers;
use Ugarit\Jetstream\Contracts\RemovesTeamMembers;
use Ugarit\Jetstream\Features;
use Ugarit\Jetstream\Jetstream;

class TeamMemberController extends Controller
{
    /**
     * Add a new team member to a team.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @return \Heritage\Http\RedirectResponse
     */
    public function store(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        if (Features::sendsTeamInvitations()) {
            app(InvitesTeamMembers::class)->invite(
                $request->user(),
                $team,
                $request->email ?: '',
                $request->role
            );
        } else {
            app(AddsTeamMembers::class)->add(
                $request->user(),
                $team,
                $request->email ?: '',
                $request->role
            );
        }

        return back(303);
    }

    /**
     * Update the given team member's role.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @param  int  $userId
     * @return \Heritage\Http\RedirectResponse
     */
    public function update(Request $request, $teamId, $userId)
    {
        app(UpdateTeamMemberRole::class)->update(
            $request->user(),
            Jetstream::newTeamModel()->findOrFail($teamId),
            $userId,
            $request->role
        );

        return back(303);
    }

    /**
     * Remove the given user from the given team.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @param  int  $userId
     * @return \Heritage\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId, $userId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(RemovesTeamMembers::class)->remove(
            $request->user(),
            $team,
            $user = Jetstream::findUserByIdOrFail($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
