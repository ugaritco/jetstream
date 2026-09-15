<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Inertia\Inertia;
use Ugarit\Jetstream\Jetstream;

class PrivacyPolicyController extends Controller
{
    /**
     * Show the privacy policy for the application.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('policy.md');

        return Inertia::render('PrivacyPolicy', [
            'policy' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }
}
