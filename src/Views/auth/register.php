<div style="display: flex; gap: 3rem; align-items: flex-start;">
    <!-- Left Column: Register Form -->
    <div style="flex: 1; max-width: 400px;">
        <h2 style="margin-bottom: 1.5rem;">Create an Account</h2>

        <?php if (isset($errors['general'])): ?>
            <div style="color: red; margin-bottom: 1rem;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/register">
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
            Already have an account? <a href="/auth/login">Log in here</a>
        </p>
    </div>

    <!-- Right Column: Website Information -->
    <div style="flex: 1; padding: 2rem; background: rgba(255,255,255,0.1); border-radius: 8px;">
        <h3 style="margin-bottom: 1rem; color: #ffc0e0;">Join WebSnap.com Today</h3>
        <p style="margin-bottom: 1rem; line-height: 1.8;">
            Create an account and start sharing your creative photos with our community.
        </p>
        <h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: #ffc0e0;">What You Get:</h4>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 0.5rem;">🎯 Your personal gallery</li>
            <li style="margin-bottom: 0.5rem;">🛠️ Advanced editing tools</li>
            <li style="margin-bottom: 0.5rem;">💬 Engage with the community</li>
            <li style="margin-bottom: 0.5rem;">🔒 Secure & private profile</li>
            <li style="margin-bottom: 0.5rem;">⭐ Showcase your work</li>
        </ul>
    </div>
</div>