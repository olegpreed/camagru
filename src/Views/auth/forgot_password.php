<?php
/** @var string $title */
/** @var array $errors */
/** @var array $old */
?>

<div class="auth-container">
    <div class="auth-form">
        <h1>Forgot Password?</h1>
        <p class="text-muted">Enter the email associated with your account and we'll send you a link to reset your password.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/forgot-password">
            <?php echo \Core\CSRF::field(); ?>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" 
                    required
                    autofocus
                >
                <?php if (!empty($errors['email'])): ?>
                    <span class="form-error"><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        </form>

        <div class="auth-links">
            <p>
                Remember your password? <a href="/auth/login">Log in</a>
            </p>
            <p>
                Don't have an account? <a href="/auth/register">Sign up</a>
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

    .text-muted {
        color: #666;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-error {
        display: block;
        color: #d9534f;
        font-size: 13px;
        margin-top: 5px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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
        text-align: center;
        font-size: 14px;
    }

    .auth-links p {
        margin: 10px 0;
    }

    .auth-links a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }
</style>
