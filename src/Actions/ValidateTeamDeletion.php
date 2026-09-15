<?php

namespace Ugarit\Jetstream\Actions;

use Heritage\Support\Facades\Gate;
use Heritage\Validation\ValidationException;

class ValidateTeamDeletion
{
    /**
     * Validate that the team can be deleted by the given user.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @return void
     */
    public function validate($user, $team)
    {
        Gate::forUser($user)->authorize('delete', $team);

        if ($team->personal_team) {
            throw ValidationException::withMessages([
                'team' => __('You may not delete your personal team.'),
            ])->errorBag('deleteTeam');
        }
    }
}
