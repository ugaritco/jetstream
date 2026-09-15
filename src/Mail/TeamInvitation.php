<?php

namespace Ugarit\Jetstream\Mail;

use Heritage\Bus\Queueable;
use Heritage\Mail\Mailable;
use Heritage\Queue\SerializesModels;
use Heritage\Support\Facades\URL;
use Ugarit\Jetstream\TeamInvitation as TeamInvitationModel;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     *
     * @var \Ugarit\Jetstream\TeamInvitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \Ugarit\Jetstream\TeamInvitation  $invitation
     * @return void
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('Team Invitation'));
    }
}
