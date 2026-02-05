<?php

namespace Core;

/**
 * CSRF Token Protection
 * Generates and validates CSRF tokens for form submissions
 */
class CSRF
{
    const TOKEN_LENGTH = 32;
    const TOKEN_LIFETIME = 3600; // 1 hour

    /**
     * Generate or retrieve CSRF token
     * 
     * @return string The CSRF token
     */
    public static function token(): string
    {
        Session::start();
        
        if (!isset($_SESSION['csrf_token']) || self::isTokenExpired()) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token from POST request
     * 
     * @param string $token The token to verify
     * @return bool True if valid, false otherwise
     */
    public static function verify(string $token): bool
    {
        Session::start();
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        if (self::isTokenExpired()) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if token has expired
     * 
     * @return bool
     */
    private static function isTokenExpired(): bool
    {
        if (!isset($_SESSION['csrf_token_time'])) {
            return true;
        }
        
        return (time() - $_SESSION['csrf_token_time']) > self::TOKEN_LIFETIME;
    }

    /**
     * Regenerate token (useful after important operations)
     */
    public static function regenerate(): void
    {
        Session::start();
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        self::token();
    }

    /**
     * Get hidden input field for forms
     * 
     * @return string HTML hidden input with CSRF token
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token()) . '">';
    }
}
