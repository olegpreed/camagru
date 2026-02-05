<?php
/** @var string $title */
/** @var string $token */
/** @var array $errors */
/** @var array $old */
?>

<div class="auth-container">
    <div class="auth-form">
        <h1>Reset Your Password</h1>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/user/reset-password?token=<?php echo urlencode($token); ?>">
            <?php echo \Core\CSRF::field(); ?>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="form-group">
                <label for="password">New Password:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autofocus
                >
                <?php if (!empty($errors['password'])): ?>
                    <span class="form-error"><?php echo htmlspecialchars($errors['password']); ?></span>
                <?php endif; ?>
                <small class="password-hint">
                    At least 8 characters with uppercase, lowercase, and numbers
                </small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm Password:</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    required
                >
                <?php if (!empty($errors['password_confirm'])): ?>
                    <span class="form-error"><?php echo htmlspecialchars($errors['password_confirm']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        </form>

        <div class="auth-links">
            <p>
                <a href="/auth/login">Back to Login</a>
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

    .password-hint {
        display: block;
        color: #999;
        font-size: 12px;
        margin-top: 5px;
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

    .auth-links a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }
</style>
