<?php

namespace Controllers;

use Core\Controller;
use Core\View;
use Middleware\AuthMiddleware;
use Models\User;
use Services\EmailService;

/**
 * Authentication Controller
 * Handles registration, login, verification, etc.
 */
class AuthController extends Controller
{
    protected function before(): bool
    {
        $guestOnlyActions = ['login', 'register', 'registerPost', 'verify'];
        $action = $this->routeParams['action'] ?? '';

        if (in_array($action, $guestOnlyActions, true)) {
            AuthMiddleware::requireGuest();
        }

        return true;
    }

    public function loginAction(): void
    {
        // If POST, process login
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->loginPostAction();
            return;
        }

        \Core\View::render('auth/login', [
            'title' => 'Login - Camagru',
            'errors' => [],
            'old' => []
        ]);
    }

    public function loginPostAction(): void
    {
        $errors = [];
        $old = $_POST ?? [];

        // CSRF verification
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!\Core\CSRF::verify($csrfToken)) {
            $errors['general'] = 'Invalid security token. Please try again.';
            \Core\View::render('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => $errors,
                'old' => $old
            ]);
            return;
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($login === '') {
            $errors['login'] = 'Username or email is required';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            \Core\View::render('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => $errors,
                'old' => $old
            ]);
            return;
        }

        $userModel = new \Models\User();
        $user = $userModel->findByLogin($login);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors['general'] = 'Invalid credentials';
            \Core\View::render('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => $errors,
                'old' => ['login' => $login]
            ]);
            return;
        }

        if (!(int)$user['is_verified']) {
            $errors['general'] = 'Please verify your email before logging in.';
            \Core\View::render('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => $errors,
                'old' => ['login' => $login]
            ]);
            return;
        }

        // Store minimal user info in session (don’t store password_hash)
        \Core\Session::set('user', [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
        // Regenerate session ID to prevent session fixation attacks
        \Core\Session::regenerate();
        header('Location: /');
        exit;
    }

    public function logoutAction(): void
    {
        // Only allow POST requests
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /');
            exit;
        }

        // CSRF verification
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!\Core\CSRF::verify($csrfToken)) {
            header('Location: /');
            exit;
        }

        \Core\Session::destroy();
        header('Location: /');
        exit;
    }
    /**
     * Show registration form or process registration
     */
    public function registerAction(): void
    {
        // If POST request, process registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->registerPostAction();
            return;
        }

        // Otherwise, show registration form (GET request)
        $data = [
            'title' => 'Register - Camagru',
            'errors' => [],
            'old' => []
        ];

        View::render('auth/register', $data);
    }

    /**
     * Process registration form
     */
    public function registerPostAction(): void
    {
        $errors = [];
        $old = $_POST ?? [];

        // CSRF verification
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!\Core\CSRF::verify($csrfToken)) {
            $errors['general'] = 'Invalid security token. Please try again.';
            $data = [
                'title' => 'Register - Camagru',
                'errors' => $errors,
                'old' => $old
            ];
            View::render('auth/register', $data);
            return;
        }

        // Get form data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate username
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (!User::validateUsername($username)) {
            $errors['username'] = 'Username must be 3-50 characters and contain only letters, numbers, and underscores';
        } else {
            $userModel = new User();
            if ($userModel->findByUsername($username)) {
                $errors['username'] = 'Username already taken';
            }
        }

        // Validate email
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!User::validateEmail($email)) {
            $errors['email'] = 'Invalid email format';
        } else {
            $userModel = $userModel ?? new User();
            if ($userModel->findByEmail($email)) {
                $errors['email'] = 'Email already registered';
            }
        }

        // Validate password
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } else {
            $passwordValidation = User::validatePassword($password);
            if (!$passwordValidation['valid']) {
                $errors['password'] = implode(', ', $passwordValidation['errors']);
            }
        }

        // Validate password confirmation
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }

        // If there are errors, show form again
        if (!empty($errors)) {
            $data = [
                'title' => 'Register - Camagru',
                'errors' => $errors,
                'old' => $old
            ];
            View::render('auth/register', $data);
            return;
        }

        // Create user
        $userModel = new User();
        $verificationToken = bin2hex(random_bytes(32)); // Generate random token
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $userModel->create([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'verification_token' => $verificationToken
        ]);

        if ($userId) {
            error_log("AuthController: sending verification email to {$email}");
            // Send verification email
            $emailService = new EmailService();
            $emailService->sendVerificationEmail($email, $username, $verificationToken);

            // Show success message
            $data = [
                'title' => 'Registration Successful - Camagru',
                'email' => $email
            ];
            View::render('auth/register-success', $data);
        } else {
            $errors['general'] = 'Registration failed. Please try again.';
            $data = [
                'title' => 'Register - Camagru',
                'errors' => $errors,
                'old' => $old
            ];
            View::render('auth/register', $data);
        }
    }

    /**
     * Verify email address
     */
    public function verifyAction(): void
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $data = [
                'title' => 'Verification Failed - Camagru',
                'message' => 'Invalid verification link.'
            ];
            View::render('auth/verify-error', $data);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByVerificationToken($token);

        if (!$user) {
            $data = [
                'title' => 'Verification Failed - Camagru',
                'message' => 'Invalid or expired verification token.'
            ];
            View::render('auth/verify-error', $data);
            return;
        }

        if ($user['is_verified']) {
            $data = [
                'title' => 'Already Verified - Camagru',
                'message' => 'Your email is already verified. You can log in.'
            ];
            View::render('auth/verify-success', $data);
            return;
        }

        // Verify the user
        if ($userModel->verify($user['id'])) {
            $data = [
                'title' => 'Email Verified - Camagru',
                'message' => 'Your email has been verified successfully! You can now log in.'
            ];
            View::render('auth/verify-success', $data);
        } else {
            $data = [
                'title' => 'Verification Failed - Camagru',
                'message' => 'Verification failed. Please try again or contact support.'
            ];
            View::render('auth/verify-error', $data);
        }
    }
}