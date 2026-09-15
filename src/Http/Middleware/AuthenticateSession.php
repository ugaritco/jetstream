<?php

namespace Ugarit\Jetstream\Http\Middleware;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

class AuthenticateSession extends BaseAuthenticateSession
{
    /**
     * Get the guard instance that should be used by the middleware.
     *
     * @return \Heritage\Contracts\Auth\Factory|\Heritage\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return app(StatefulGuard::class);
    }
}
