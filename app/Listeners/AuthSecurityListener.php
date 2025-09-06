<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Request;

class AuthSecurityListener
{
    /**
     * Handle successful login
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        
        if ($user) {
            $user->updateLoginInfo(
                Request::ip(),
                Request::userAgent() ?? 'Unknown'
            );
        }
    }

    /**
     * Handle failed login
     */
    public function handleFailedLogin(Failed $event): void
    {
        if ($event->user) {
            $event->user->incrementFailedLoginAttempts();
        }
    }

    /**
     * Handle password reset
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        $user = $event->user;
        
        if ($user) {
            $user->markPasswordAsChanged();
        }
    }

    /**
     * Register the listeners for the subscriber
     */
    public function subscribe($events): void
    {
        $events->listen(
            Login::class,
            [self::class, 'handleLogin']
        );

        $events->listen(
            Failed::class,
            [self::class, 'handleFailedLogin']
        );

        $events->listen(
            PasswordReset::class,
            [self::class, 'handlePasswordReset']
        );
    }
}
