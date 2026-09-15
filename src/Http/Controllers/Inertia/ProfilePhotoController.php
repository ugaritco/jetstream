<?php

namespace Ugarit\Jetstream\Http\Controllers\Inertia;

use Heritage\Http\Request;
use Heritage\Routing\Controller;

class ProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->deleteProfilePhoto();

        return back(303)->with('status', 'profile-photo-deleted');
    }
}
