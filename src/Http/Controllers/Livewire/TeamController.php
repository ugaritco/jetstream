<?php

namespace Ugarit\Jetstream\Http\Controllers\Livewire;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\Gate;
use Ugarit\Jetstream\Jetstream;

class TeamController extends Controller
{
    /**
     * Show the team management screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  int  $teamId
     * @return \Heritage\View\View
     */
    public function show(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        if (Gate::denies('view', $team)) {
            abort(403);
        }

        return view('teams.show', [
            'user' => $request->user(),
            'team' => $team,
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\View\View
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Jetstream::newTeamModel());

        return view('teams.create', [
            'user' => $request->user(),
        ]);
    }
}
