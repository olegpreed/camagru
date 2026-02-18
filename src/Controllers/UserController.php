<?php

namespace Controllers;

use Core\Controller;
use Core\CSRF;
use Core\RateLimiter;
use Core\View;
use Middleware\AuthMiddleware;
use Models\User;
use Services\EmailService;

/**
 * User Controller
 * Handles user profile, password reset, and account management
 */
class UserController extends Controller
{
	protected function before(): bool
	{
		// Most actions require authentication
		$publicActions = ['forgotPassword', 'resetPassword', 'verifyEmailChange'];
		$action = $this->routeParams['action'] ?? '';

		if (!in_array($action, $publicActions, true)) {
			AuthMiddleware::requireAuth();
		}

		return true;
	}

	/**
	 * Show forgot password form
	 */
	public function forgotPasswordAction(): void
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->forgotPasswordPostAction();
			return;
		}

		View::render('auth/forgot_password', [
			'title' => 'Forgot Password - Camagru',
			'errors' => [],
			'old' => []
		]);
	}

	/**
	 * Process forgot password request
	 */
	public function forgotPasswordPostAction(): void
	{
		$errors = [];
		$old = $_POST ?? [];

		// CSRF verification
		$csrfToken = $_POST['csrf_token'] ?? '';
		if (!CSRF::verify($csrfToken)) {
			$errors['general'] = 'Invalid security token. Please try again.';
			View::render('auth/forgot_password', [
				'title' => 'Forgot Password - Camagru',
				'errors' => $errors,
				'old' => $old
			]);
			return;
		}

		$email = trim($_POST['email'] ?? '');

		if (empty($email)) {
			$errors['email'] = 'Email is required';
		} elseif (!User::validateEmail($email)) {
			$errors['email'] = 'Invalid email format';
		}

		if (!empty($errors)) {
			View::render('auth/forgot_password', [
				'title' => 'Forgot Password - Camagru',
				'errors' => $errors,
				'old' => $old
			]);
			return;
		}

		$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		$rateKey = 'password_reset:' . strtolower($email) . '|' . $ip;
		$limitInfo = RateLimiter::hit($rateKey, 3, 60 * 60);
		if (!$limitInfo['allowed']) {
			$retryMinutes = max(1, (int)ceil($limitInfo['retry_after'] / 60));
			$errors['general'] = 'Too many password reset attempts. Please try again in ' . $retryMinutes . ' minute(s).';
			View::render('auth/forgot_password', [
				'title' => 'Forgot Password - Camagru',
				'errors' => $errors,
				'old' => $old
			]);
			return;
		}

		$userModel = new User();
		$user = $userModel->findByEmail($email);

		// For security, don't reveal if email exists or not
		if ($user) {
			$resetToken = bin2hex(random_bytes(32));
			$expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration

			if ($userModel->setResetToken($user['id'], $resetToken, $expiresAt)) {
				$emailService = new EmailService();
				$emailService->sendPasswordResetEmail($user['email'], $user['username'], $resetToken);
			}
		}

		// Show success message regardless
		View::render('auth/forgot_password_sent', [
			'title' => 'Check Your Email - Camagru',
			'email' => $email
		]);
	}

	/**
	 * Reset password with token
	 */
	public function resetPasswordAction(): void
	{
		$token = $_GET['token'] ?? '';

		if (empty($token)) {
			View::render('auth/verify-error', [
				'title' => 'Invalid Reset Link - Camagru',
				'message' => 'Invalid or missing reset token.'
			]);
			return;
		}

		// Verify token exists and is not expired
		$userModel = new User();
		$user = $userModel->findByResetToken($token);

		if (!$user) {
			View::render('auth/verify-error', [
				'title' => 'Invalid Reset Link - Camagru',
				'message' => 'Invalid or expired reset token.'
			]);
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->resetPasswordPostAction($token, $user['id']);
			return;
		}

		View::render('auth/reset_password', [
			'title' => 'Reset Password - Camagru',
			'token' => $token,
			'errors' => [],
			'old' => []
		]);
	}

	/**
	 * Process password reset
	 */
	public function resetPasswordPostAction(string $token, int $userId): void
	{
		$errors = [];
		$old = $_POST ?? [];

		// CSRF verification
		$csrfToken = $_POST['csrf_token'] ?? '';
		if (!CSRF::verify($csrfToken)) {
			$errors['general'] = 'Invalid security token. Please try again.';
			View::render('auth/reset_password', [
				'title' => 'Reset Password - Camagru',
				'token' => $token,
				'errors' => $errors,
				'old' => $old
			]);
			return;
		}

		$password = $_POST['password'] ?? '';
		$passwordConfirm = $_POST['password_confirm'] ?? '';

		// Validate password
		if (empty($password)) {
			$errors['password'] = 'Password is required';
		} else {
			$passwordValidation = User::validatePassword($password);
			if (!$passwordValidation['valid']) {
				$errors['password'] = implode(', ', $passwordValidation['errors']);
			}
		}

		// Validate confirmation
		if ($password !== $passwordConfirm) {
			$errors['password_confirm'] = 'Passwords do not match';
		}

		if (!empty($errors)) {
			View::render('auth/reset_password', [
				'title' => 'Reset Password - Camagru',
				'token' => $token,
				'errors' => $errors,
				'old' => []
			]);
			return;
		}

		// Update password and clear reset token
		$userModel = new User();
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);

		if ($userModel->updatePassword($userId, $passwordHash)) {
			$userModel->clearResetToken($userId);

			View::render('auth/verify-success', [
				'title' => 'Password Reset Successful - Camagru',
				'message' => 'Your password has been reset successfully. You can now log in with your new password.'
			]);
		} else {
			$errors['general'] = 'Password reset failed. Please try again.';
			View::render('auth/reset_password', [
				'title' => 'Reset Password - Camagru',
				'token' => $token,
				'errors' => $errors,
				'old' => []
			]);
		}
	}

	/**
	 * Show user profile
	 */
	public function profileAction(): void
	{
		$user = AuthMiddleware::user();
		$userModel = new User();
		$fullUser = $userModel->findById($user['id']);

		// Build success message based on query params
		$successMessage = '';
		if (isset($_GET['updated'])) {
			if (isset($_GET['email_verification_sent'])) {
				$successMessage = 'Profile updated! A verification email has been sent to your new email address. Please check your inbox to complete the email change.';
			} else {
				$successMessage = 'Profile updated successfully!';
			}
		} elseif (isset($_GET['password_changed'])) {
			$successMessage = 'Password changed successfully!';
		}

		View::render('user/profile', [
			'title' => 'My Profile - Camagru',
			'user' => $fullUser,
			'successMessage' => $successMessage,
			'errors' => []
		]);
	}

	/**
	 * Edit user profile
	 */
	public function editProfileAction(): void
	{
		$user = AuthMiddleware::user();
		$userModel = new User();
		$fullUser = $userModel->findById($user['id']);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->editProfilePostAction($user['id']);
			return;
		}

		View::render('user/profile', [
			'title' => 'Edit Profile - Camagru',
			'user' => $fullUser,
			'isEditing' => true,
			'successMessage' => '',
			'errors' => []
		]);
	}

	/**
	 * Process profile update (username, email)
	 */
	public function editProfilePostAction(int $userId): void
	{
		$errors = [];
		$old = $_POST ?? [];

		// CSRF verification
		$csrfToken = $_POST['csrf_token'] ?? '';
		if (!CSRF::verify($csrfToken)) {
			$errors['general'] = 'Invalid security token. Please try again.';
			$this->renderProfileWithErrors($userId, $errors, $old);
			return;
		}

		$username = trim($_POST['username'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$commentNotifications = isset($_POST['comment_notifications']) ? 1 : 0;
		$userModel = new User();
		$currentUser = $userModel->findById($userId);

		// Validate username
		if (empty($username)) {
			$errors['username'] = 'Username is required';
		} elseif (!User::validateUsername($username)) {
			$errors['username'] = 'Username must be 3-50 characters and contain only letters, numbers, and underscores';
		} elseif ($username !== $currentUser['username']) {
			// Check if new username is taken
			if ($userModel->findByUsername($username)) {
				$errors['username'] = 'Username already taken';
			}
		}

		// Validate email
		$emailChanged = false;
		if (empty($email)) {
			$errors['email'] = 'Email is required';
		} elseif (!User::validateEmail($email)) {
			$errors['email'] = 'Invalid email format';
		} elseif ($email !== $currentUser['email']) {
			$emailChanged = true;
			// Check if new email is taken
			if ($userModel->findByEmail($email)) {
				$errors['email'] = 'Email already registered';
			}
		}

		if (!empty($errors)) {
			$this->renderProfileWithErrors($userId, $errors, $old);
			return;
		}

		// Update profile (username and notifications only)
		if ($userModel->updateProfile($userId, $username, $commentNotifications)) {
			// Update session with new username
			\Core\Session::set('user', [
				'id' => $userId,
				'username' => $username,
				'email' => $currentUser['email'], // Keep old email until verified
			]);

			// Handle email change if needed
			if ($emailChanged) {
				$verificationToken = bin2hex(random_bytes(32));
				$expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration

				if ($userModel->setPendingEmail($userId, $email, $verificationToken, $expiresAt)) {
					$emailService = new EmailService();
					$emailService->sendEmailChangeVerification($email, $username, $verificationToken);

					// Redirect with email verification message
					header('Location: /user/profile?updated=1&email_verification_sent=1');
					exit;
				}
			}

			// Redirect to profile (username updated, no email change)
			header('Location: /user/profile?updated=1');
			exit;
		} else {
			$errors['general'] = 'Profile update failed. Please try again.';
			$this->renderProfileWithErrors($userId, $errors, $old);
		}
	}

	/**
	 * Change password
	 */
	public function changePasswordAction(): void
	{
		$user = AuthMiddleware::user();
		$userModel = new User();
		$fullUser = $userModel->findById($user['id']);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->changePasswordPostAction($user['id']);
			return;
		}

		View::render('user/change_password', [
			'title' => 'Change Password - Camagru',
			'user' => $fullUser,
			'errors' => []
		]);
	}

	/**
	 * Process password change
	 */
	public function changePasswordPostAction(int $userId): void
	{
		$errors = [];
		$userModel = new User();
		$fullUser = $userModel->findById($userId);

		// CSRF verification
		$csrfToken = $_POST['csrf_token'] ?? '';
		if (!CSRF::verify($csrfToken)) {
			$errors['general'] = 'Invalid security token. Please try again.';
			View::render('user/change_password', [
				'title' => 'Change Password - Camagru',
				'user' => $fullUser,
				'errors' => $errors
			]);
			return;
		}

		$currentPassword = $_POST['current_password'] ?? '';
		$newPassword = $_POST['new_password'] ?? '';
		$confirmPassword = $_POST['confirm_password'] ?? '';

		// Verify current password
		if (empty($currentPassword)) {
			$errors['current_password'] = 'Current password is required';
		}

		if (!empty($currentPassword) && !password_verify($currentPassword, $fullUser['password_hash'])) {
			$errors['current_password'] = 'Current password is incorrect';
		}

		// Validate new password
		if (empty($newPassword)) {
			$errors['new_password'] = 'New password is required';
		} else {
			$passwordValidation = User::validatePassword($newPassword);
			if (!$passwordValidation['valid']) {
				$errors['new_password'] = implode(', ', $passwordValidation['errors']);
			}
		}

		// Check passwords match
		if ($newPassword !== $confirmPassword) {
			$errors['confirm_password'] = 'Passwords do not match';
		}

		if (!empty($errors)) {
			View::render('user/change_password', [
				'title' => 'Change Password - Camagru',
				'user' => $fullUser,
				'errors' => $errors
			]);
			return;
		}

		// Update password
		$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
		if ($userModel->updatePassword($userId, $passwordHash)) {
			header('Location: /user/profile?password_changed=1');
			exit;
		} else {
			$errors['general'] = 'Password change failed. Please try again.';
			View::render('user/change_password', [
				'title' => 'Change Password - Camagru',
				'user' => $fullUser,
				'errors' => $errors
			]);
		}
	}

	/**
	 * Verify email change with token
	 */
	public function verifyEmailChangeAction(): void
	{
		$token = $_GET['token'] ?? '';

		if (empty($token)) {
			View::render('auth/verify-error', [
				'title' => 'Invalid Verification Link - Camagru',
				'message' => 'Invalid or missing verification token.'
			]);
			return;
		}

		$userModel = new User();
		$user = $userModel->findByPendingEmailToken($token);

		if (!$user) {
			View::render('auth/verify-error', [
				'title' => 'Invalid Verification Link - Camagru',
				'message' => 'Invalid or expired verification token.'
			]);
			return;
		}

		// Confirm the email change
		if ($userModel->confirmEmailChange($user['id'])) {
			// Update session if this is the current user
			$currentUser = AuthMiddleware::user();
			if ($currentUser && $currentUser['id'] == $user['id']) {
				\Core\Session::set('user', [
					'id' => $currentUser['id'],
					'username' => $currentUser['username'],
					'email' => $user['pending_email'], // New verified email
				]);
			}

			View::render('auth/verify-success', [
				'title' => 'Email Changed Successfully - Camagru',
				'message' => 'Your email address has been changed successfully! You can now use your new email address to log in.'
			]);
		} else {
			View::render('auth/verify-error', [
				'title' => 'Verification Failed - Camagru',
				'message' => 'Email change failed. Please try again or contact support.'
			]);
		}
	}

	/**
	 * Helper method to render profile with errors
	 */
	private function renderProfileWithErrors(int $userId, array $errors, array $old): void
	{
		$userModel = new User();
		$fullUser = $userModel->findById($userId);

		View::render('user/profile', [
			'title' => 'Edit Profile - Camagru',
			'user' => $fullUser,
			'isEditing' => true,
			'errors' => $errors,
			'old' => $old
		]);
	}
}
