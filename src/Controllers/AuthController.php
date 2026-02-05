<?php

namespace Controllers;

use Core\Controller;
use Core\View;
use Models\User;
use Services\EmailService;

/**
 * Authentication Controller
 * Handles registration, login, verification, etc.
 */
class AuthController extends Controller
{
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