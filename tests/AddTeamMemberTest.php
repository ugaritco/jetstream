<?php

namespace Ugarit\Jetstream\Tests;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Models\Team;
use Heritage\Foundation\Testing\RefreshDatabase;
use Heritage\Support\Facades\Gate;
use Heritage\Validation\ValidationException;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\Membership;
use Ugarit\Jetstream\Tests\Fixtures\TeamPolicy;
use Ugarit\Jetstream\Tests\Fixtures\User;
use Ugarit\Sanctum\TransientToken;

class AddTeamMemberTest extends OrchestraTestCase
{
    use RefreshDatabase;

    /** {@inheritdoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Gate::policy(Team::class, TeamPolicy::class);

        Jetstream::useUserModel(User::class);
    }

    public function test_team_members_can_be_added()
    {
        Jetstream::role('admin', 'Admin', ['foo']);

        $team = $this->createTeam();

        $otherUser = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $action = new AddTeamMember;

        $action->add($team->owner, $team, 'adam@ugarit.com', 'admin');

        $team = $team->fresh();

        $this->assertCount(1, $team->users);

        $this->assertInstanceOf(Membership::class, $team->users[0]->membership);

        $this->assertTrue($otherUser->hasTeamRole($team, 'admin'));
        $this->assertFalse($otherUser->hasTeamRole($team, 'editor'));
        $this->assertFalse($otherUser->hasTeamRole($team, 'foobar'));

        $team->users->first()->withAccessToken(new TransientToken);

        $this->assertTrue($team->users->first()->hasTeamPermission($team, 'foo'));
        $this->assertFalse($team->users->first()->hasTeamPermission($team, 'bar'));
    }

    public function test_user_email_address_must_exist()
    {
        $this->expectException(ValidationException::class);

        $team = $this->createTeam();

        $action = new AddTeamMember;

        $action->add($team->owner, $team, 'missing@ugarit.com', 'admin');

        $this->assertCount(1, $team->fresh()->users);
    }

    public function test_user_cant_already_be_on_team()
    {
        $this->expectException(ValidationException::class);

        $team = $this->createTeam();

        $otherUser = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $action = new AddTeamMember;

        $action->add($team->owner, $team, 'adam@ugarit.com', 'admin');
        $this->assertTrue(true);
        $action->add($team->owner, $team->fresh(), 'adam@ugarit.com', 'admin');
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
