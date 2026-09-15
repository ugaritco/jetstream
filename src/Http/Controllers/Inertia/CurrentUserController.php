<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Validation\ValidationException;
use Inertia\Inertia;
use Ugarit\Fortify\Actions\ConfirmPassword;
use Ugarit\Jetstream\Contracts\DeletesUsers;

class CurrentUserController extends Controller
{
    /**
     * Delete the current user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @return \Heritage\Http\Response
     */
    public function destroy(Request $request, StatefulGuard $guard)
    {
        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        app(DeletesUsers::class)->delete($request->user()->fresh());

        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(url('/'));
    }
}
