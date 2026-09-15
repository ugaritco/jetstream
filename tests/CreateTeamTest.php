<?php

namespace Ugarit\Jetstream\Tests;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Team;
use Heritage\Support\Facades\Gate;
use Heritage\Validation\ValidationException;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\Tests\Fixtures\TeamPolicy;
use Ugarit\Jetstream\Tests\Fixtures\User;

class CreateTeamTest extends OrchestraTestCase
{
    /** {@inheritdoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Gate::policy(Team::class, TeamPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_team_name_can_be_updated()
    {
        $action = new CreateTeam;

        $user = User::forceCreate([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@ugarit.com',
            'password' => 'secret',
        ]);

        $team = $action->create($user, ['name' => 'Test Team']);

        $this->assertInstanceOf(Team::class, $team);
    }

    public function test_name_is_required()
    {
        $this->expectException(ValidationException::class);

        $action = new CreateTeam;

        $user = User::forceCreate([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@ugarit.com',
            'password' => 'secret',
        ]);

        $action->create($user, ['name' => '']);
    }
}
