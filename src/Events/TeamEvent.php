<?php

namespace Ugarit\Jetstream\Events;

use Heritage\Broadcasting\InteractsWithSockets;
use Heritage\Foundation\Events\Dispatchable;
use Heritage\Queue\SerializesModels;

abstract class TeamEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The team instance.
     *
     * @var \App\Models\Team
     */
    public $team;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function __construct($team)
    {
        $this->team = $team;
    }
}
