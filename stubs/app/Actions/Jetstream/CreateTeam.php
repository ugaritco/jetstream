<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Heritage\Support\Facades\Gate;
use Heritage\Support\Facades\Validator;
use Ugarit\Jetstream\Contracts\CreatesTeams;
use Ugarit\Jetstream\Events\AddingTeam;
use Ugarit\Jetstream\Jetstream;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Team
    {
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createTeam');

        AddingTeam::dispatch($user);

        $user->switchTeam($team = $user->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
        ]));

        return $team;
    }
}
