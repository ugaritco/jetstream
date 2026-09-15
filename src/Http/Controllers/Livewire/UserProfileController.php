<?php

namespace Ugarit\Jetstream\Http\Controllers\Livewire;

use Heritage\Http\Request;
use Heritage\Routing\Controller;

class UserProfileController extends Controller
{
    /**
     * Show the user profile screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\View\View
     */
    public function show(Request $request)
    {
        return view('profile.show', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
