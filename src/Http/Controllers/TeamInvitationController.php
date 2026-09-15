<?php

namespace Ugarit\Jetstream\Http\Controllers;

use Heritage\Auth\Access\AuthorizationException;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Gate;
use Ugarit\Jetstream\Contracts\AddsTeamMembers;
use Ugarit\Jetstream\Jetstream;

class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $invitationId
     * @return \Heritage\Http\RedirectResponse
     */
    public function accept(Request $request, $invitationId)
    {
        $model = Jetstream::teamInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        app(AddsTeamMembers::class)->add(
            $invitation->team->owner,
            $invitation->team,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :team team.', ['team' => $invitation->team->name]),
        );
    }

    /**
     * Cancel the given team invitation.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $invitationId
     * @return \Heritage\Http\RedirectResponse
     */
    public function destroy(Request $request, $invitationId)
    {
        $model = Jetstream::teamInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
