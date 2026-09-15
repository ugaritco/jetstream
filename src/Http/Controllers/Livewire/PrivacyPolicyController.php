<?php

namespace Ugarit\Jetstream\Http\Controllers\Livewire;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Ugarit\Jetstream\Jetstream;

class PrivacyPolicyController extends Controller
{
    /**
     * Show the privacy policy for the application.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\View\View
     */
    public function show(Request $request)
    {
        $policyFile = Jetstream::localizedMarkdownPath('policy.md');

        return view('policy', [
            'policy' => Str::markdown(file_get_contents($policyFile)),
        ]);
    }
}
