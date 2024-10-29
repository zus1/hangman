<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class AuthLoginListener extends AuthEventListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $this->refreshApiKey($user);
    }
}
