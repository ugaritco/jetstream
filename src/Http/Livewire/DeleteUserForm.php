<?php

namespace Ugarit\Jetstream\Http\Livewire;

use Heritage\Contracts\Auth\StatefulGuard;
use Heritage\Http\Request;
use Heritage\Support\Facades\Auth;
use Heritage\Support\Facades\Hash;
use Heritage\Validation\ValidationException;
use Ugarit\Jetstream\Contracts\DeletesUsers;
use Livewire\Component;

class DeleteUserForm extends Component
{
    /**
     * Indicates if user deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingUserDeletion = false;

    /**
     * The user's current password.
     *
     * @var string
     */
    public $password = '';

    /**
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmUserDeletion()
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatch('confirming-delete-user');

        $this->confirmingUserDeletion = true;
    }

    /**
     * Delete the current user.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Jetstream\Contracts\DeletesUsers  $deleter
     * @param  \Heritage\Contracts\Auth\StatefulGuard  $auth
     * @return \Heritage\Routing\Redirector|\Heritage\Http\RedirectResponse
     */
    public function deleteUser(Request $request, DeletesUsers $deleter, StatefulGuard $auth)
    {
        $this->resetErrorBag();

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $deleter->delete(Auth::user()->fresh());

        $auth->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect(config('fortify.redirects.logout') ?? '/');
    }

    /**
     * Render the component.
     *
     * @return \Heritage\View\View
     */
    public function render()
    {
        return view('profile.delete-user-form');
    }
}
