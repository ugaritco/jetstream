<?php

namespace Ugarit\Jetstream\Tests;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Team;
use Heritage\Support\Facades\Gate;
use Heritage\Support\Facades\URL;
use Ugarit\Jetstream\Contracts\AddsTeamMembers;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\Tests\Fixtures\TeamPolicy;
use Ugarit\Jetstream\Tests\Fixtures\User;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('jetstream.stack', 'inertia')]
#[WithConfig('jetstream.features', ['teams'])]
class TeamInvitationControllerTest extends OrchestraTestCase
{
    /** {@inheritdoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Gate::policy(Team::class, TeamPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_team_invitations_can_be_accepted()
    {
        $this->mock(AddsTeamMembers::class)->shouldReceive('add')->once();

        Jetstream::role('admin', 'Admin', ['foo', 'bar']);
        Jetstream::role('editor', 'Editor', ['baz', 'qux']);

        $team = $this->createTeam();

        $invitation = $team->teamInvitations()->create(['email' => 'adam@ugarit.com', 'role' => 'admin']);

        $url = URL::signedRoute('team-invitations.accept', ['invitation' => $invitation]);

        $response = $this->actingAs($team->owner)->get($url);

        $response->assertRedirect();
    }

    protected function createTeam()
    {
        $action = new CreateTeam;

        $user = User::forceCreate([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@ugarit.com',
            'password' => 'secret',
        ]);

        return $action->create($user, ['name' => 'Test Team']);
    }
}
