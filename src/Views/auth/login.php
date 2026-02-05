<h2>Log In</h2>

<?php if (!empty($errors['general'])): ?>
    <div style="color: red; margin-bottom: 1rem;">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/auth/login" style="max-width: 400px;">
    <div style="margin-bottom: 1rem;">
        <label for="login">Username or Email:</label><br>
        <input
            type="text"
            id="login"
            name="login"
            value="<?= htmlspecialchars($old['login'] ?? '') ?>"
            required
            style="width: 100%; padding: 0.5rem; margin-top: 0.25rem;"
        >
        <?php if (!empty($errors['login'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['login']) ?>
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
        <?php if (!empty($errors['password'])): ?>
            <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">
                <?= htmlspecialchars($errors['password']) ?>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" style="width: 100%; padding: 0.75rem; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Log In
    </button>
</form>

<p style="margin-top: 1rem;">
    Don’t have an account? <a href="/auth/register">Register</a>
</p>
<p style="margin-top: 0.5rem;">
	Forgot your password? <a href="/auth/forgot-password">Reset it</a>