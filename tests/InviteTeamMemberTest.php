<?php

namespace Ugarit\Jetstream\Tests;

use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\InviteTeamMember;
use App\Models\Team;
use Heritage\Support\Facades\Gate;
use Heritage\Support\Facades\Mail;
use Heritage\Validation\ValidationException;
use Ugarit\Jetstream\Jetstream;
use Ugarit\Jetstream\Tests\Fixtures\TeamPolicy;
use Ugarit\Jetstream\Tests\Fixtures\User;

class InviteTeamMemberTest extends OrchestraTestCase
{
    /** {@inheritdoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Gate::policy(Team::class, TeamPolicy::class);
        Jetstream::useUserModel(User::class);
    }

    public function test_team_members_can_be_invited()
    {
        Mail::fake();

        Jetstream::role('admin', 'Admin', ['foo']);

        $team = $this->createTeam();

        $otherUser = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $action = new InviteTeamMember;

        $action->invite($team->owner, $team, 'adam@ugarit.com', 'admin');

        $team = $team->fresh();

        $this->assertCount(0, $team->users);
        $this->assertCount(1, $team->teamInvitations);
        $this->assertEquals('adam@ugarit.com', $team->teamInvitations->first()->email);
        $this->assertEquals($team->id, $team->teamInvitations->first()->team->id);
    }

    public function test_user_cant_already_be_on_team()
    {
        Mail::fake();

        $this->expectException(ValidationException::class);

        $team = $this->createTeam();

        $otherUser = User::forceCreate([
            'name' => 'Adam Wathan',
            'email' => 'adam@ugarit.com',
            'password' => 'secret',
        ]);

        $action = new InviteTeamMember;

        $action->invite($team->owner, $team, 'adam@ugarit.com', 'admin');
        $this->assertTrue(true);
        $action->invite($team->owner, $team->fresh(), 'adam@ugarit.com', 'admin');
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
