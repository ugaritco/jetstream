<?php

namespace Ugarit\Jetstream\Tests;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Team;
use Heritage\Support\Facades\Gate;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\Tests\Fixtures\TeamPolicy;
use Ugarit\Jetstream\Tests\Fixtures\User;
use Ugarit\Sanctum\TransientToken;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('jetstream.stack', 'inertia')]
#[WithConfig('jetstream.features', ['teams'])]
class TeamMemberControllerTest extends OrchestraTestCase
{
    /** {@inheritdoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Gate::policy(Team::class, TeamPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_team_member_permissions_can_be_updated()
    {
        Jetstream::role('admin', 'Admin', ['foo', 'bar']);
        Jetstream::role('editor', 'Editor', ['baz', 'qux']);

        $team = $this->createTeam();

        $adam = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $team->users()->attach($adam, ['role' => 'admin']);

        $response = $this->actingAs($team->owner)->put('/teams/'.$team->id.'/members/'.$adam->id, [
            'role' => 'editor',
        ]);

        $response->assertRedirect();

        $adam = $adam->fresh();

        $adam->withAccessToken(new TransientToken);

        $this->assertTrue($adam->hasTeamPermission($team, 'baz'));
        $this->assertTrue($adam->hasTeamPermission($team, 'qux'));
    }

    public function test_team_member_permissions_cant_be_updated_if_not_authorized()
    {
        $team = $this->createTeam();

        $adam = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $team->users()->attach($adam, ['role' => 'admin']);

        $response = $this->actingAs($adam)->put('/teams/'.$team->id.'/members/'.$adam->id, [
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
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
