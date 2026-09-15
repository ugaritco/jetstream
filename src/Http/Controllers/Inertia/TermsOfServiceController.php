<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Inertia\Inertia;
use Ugarit\Jetstream\Jetstream;

class TermsOfServiceController extends Controller
{
    /**
     * Show the terms of service for the application.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $termsFile = Jetstream::localizedMarkdownPath('terms.md');

        return Inertia::render('TermsOfService', [
            'terms' => Str::markdown(file_get_contents($termsFile)),
        ]);
    }
}
