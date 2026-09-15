<?php

namespace Ugarit\Jetstream\Tests\Fixtures;

use Heritage\Foundation\Auth\User as Authenticatable;
use Ugarit\Jetstream\HasProfilePhoto;
use Ugarit\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasProfilePhoto;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
