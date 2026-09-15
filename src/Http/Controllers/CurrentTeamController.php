<?php

namespace Ugarit\Jetstream\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Jetstream\Jetstream;

class CurrentTeamController extends Controller
{
    /**
     * Update the authenticated user's current team.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $team = Jetstream::newTeamModel()->findOrFail($request->team_id);

        if (! $request->user()->switchTeam($team)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }
}
