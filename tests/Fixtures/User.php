<?php

namespace Ugarit\Jetstream\Tests\Fixtures;

use App\Models\User as BaseUser;
use Ugarit\Jetstream\HasProfilePhoto;
use Ugarit\Jetstream\HasTeams;
use Ugarit\Sanctum\HasApiTokens;

class User extends BaseUser
{
    use HasApiTokens, HasTeams, HasProfilePhoto;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
