<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Facades\DB;
use Heritage\Validation\ValidationException;
use Ugarit\Fortify\Actions\ConfirmPassword;

class OtherBrowserSessionsController extends Controller
{
    /**
     * Log out from other browser sessions.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $guard
     * @return \Heritage\Http\RedirectResponse
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

        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return back(303);
    }

    /**
     * Delete the other browser session records from storage.
     *
     * @param  \Heritage\Http\Request  $request
     * @return void
     */
    protected function deleteOtherSessionRecords(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
