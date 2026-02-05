<?php
/** @var string $title */
/** @var string $email */
?>

<div class="auth-container">
    <div class="auth-form">
        <h1>Check Your Email</h1>

        <div class="alert alert-success">
            <strong>Success!</strong> We've sent a password reset link to <strong><?php echo htmlspecialchars($email); ?></strong>
        </div>

        <p>
            Please check your email for a link to reset your password. The link will expire in 1 hour.
        </p>

        <p class="text-muted">
            Didn't receive the email? Check your spam folder or <a href="/auth/forgot-password">try again</a>.
        </p>

        <div class="auth-links">
            <p>
                <a href="/auth/login" class="btn btn-primary btn-block">Back to Login</a>
            </p>
        </div>
    </div>
</div>

<style>
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .auth-form {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }

    .auth-form h1 {
        margin-top: 0;
        color: #333;
        text-align: center;
        margin-bottom: 30px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-success strong {
        font-weight: 600;
    }

    p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .text-muted {
        font-size: 13px;
    }

    .text-muted a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .text-muted a:hover {
        text-decoration: underline;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-block {
        width: 100%;
        text-align: center;
    }

    .auth-links {
        margin-top: 30px;
    }

    .auth-links p {
        margin: 0;
    }
</style>
