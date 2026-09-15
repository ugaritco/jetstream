<?php

namespace Ugarit\Jetstream\Http\Livewire;

use Heritage\Support\Facades\Auth;
use Ugarit\Jetstream\Contracts\CreatesTeams;
use Ugarit\Jetstream\RedirectsActions;
use Livewire\Component;

class CreateTeamForm extends Component
{
    use RedirectsActions;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * Create a new team.
     *
     * @param  \Ugarit\Jetstream\Contracts\CreatesTeams  $creator
     * @return mixed
     */
    public function createTeam(CreatesTeams $creator)
    {
        $this->resetErrorBag();

        $creator->create(Auth::user(), $this->state);

        return $this->redirectPath($creator);
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Heritage\View\View
     */
    public function render()
    {
        return view('teams.create-team-form');
    }
}
