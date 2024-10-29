<?php

namespace App\Listeners;

use App\Constant\TokenType;
use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cookie;

class AuthLogoutListener extends AuthEventListener
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $this->expireApiKey();
    }
}
