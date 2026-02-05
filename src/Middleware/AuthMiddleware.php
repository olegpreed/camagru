<?php

namespace Middleware;

use Core\Session;

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!Session::has('user')) {
            header('Location: /auth/login');
            exit;
        }
    }

    public static function requireGuest(): void
    {
        if (Session::has('user')) {
            header('Location: /');
            exit;
        }
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }
}