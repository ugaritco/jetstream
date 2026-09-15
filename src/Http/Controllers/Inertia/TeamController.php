<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Gate;
use Ugarit\Jetstream\Actions\ValidateTeamDeletion;
use Ugarit\Jetstream\Contracts\CreatesTeams;
use Ugarit\Jetstream\Contracts\DeletesTeams;
use Ugarit\Jetstream\Contracts\UpdatesTeamNames;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\RedirectsActions;

class TeamController extends Controller
{
    use RedirectsActions;

    /**
     * Show the team management screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @return \Inertia\Response
     */
    public function show(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        Gate::authorize('view', $team);

        return Jetstream::inertia()->render($request, 'Teams/Show', [
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
                'canUpdateTeamMembers' => Gate::check('updateTeamMember', $team),
            ],
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Jetstream::newTeamModel());

        return Jetstream::inertia()->render($request, 'Teams/Create');
    }

    /**
     * Create a new team.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $creator = app(CreatesTeams::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Update the given team's name.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @return \Heritage\Http\RedirectResponse
     */
    public function update(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

        return back(303);
    }

    /**
     * Delete the given team.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @return \Heritage\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return $this->redirectPath($deleter);
    }
}
