<?php

namespace Ugarit\Jetstream\Http\Controllers\Livewire;

use Heritage\Http\Request;
use Heritage\Routing\Controller;

class ApiTokenController extends Controller
{
    /**
     * Show the user API token screen.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\View\View
     */
    public function index(Request $request)
    {
        return view('api.index', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
