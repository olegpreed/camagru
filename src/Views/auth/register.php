<h2>Create an Account</h2>

<?php if (isset($errors['general'])): ?>
    <div style="color: red; margin-bottom: 1rem;">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/auth/register" style="max-width: 400px;">
    <?= \Core\CSRF::field() ?>
    
    <div style="margin-bottom: 1rem;">
        <label for="username">Username:</label><br>
        <input 
            type="text" 
            id="username" 
            name="username" 
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            required
            style="width: 100%; padding: 0.5rem; margin-top: 0.25rem;"
        >
        <?php if (isset($errors['username'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['username']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="email">Email:</label><br>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            required
            style="width: 100%; padding: 0.5rem; margin-top: 0.25rem;"
        >
        <?php if (isset($errors['email'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['email']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="password">Password:</label><br>
        <input 
            type="password" 
            id="password" 
            name="password" 
            required
            style="width: 100%; padding: 0.5rem; margin-top: 0.25rem;"
        >
        <?php if (isset($errors['password'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['password']) ?>
            </div>
        <?php endif; ?>
        <small style="color: #666; font-size: 0.875rem;">
            Must be at least 8 characters with uppercase, lowercase, and number
        </small>
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="password_confirm">Confirm Password:</label><br>
        <input 
            type="password" 
            id="password_confirm" 
            name="password_confirm" 
            required
            style="width: 100%; padding: 0.5rem; margin-top: 0.25rem;"
        >
        <?php if (isset($errors['password_confirm'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['password_confirm']) ?>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" style="width: 100%; padding: 0.75rem; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Register
    </button>
</form>

<p style="margin-top: 1rem;">
    Already have an account? <a href="/auth/login">Log in</a>
</p>