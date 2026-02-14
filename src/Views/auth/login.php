<div style="display: flex; gap: 3rem; align-items: flex-start;">
    <!-- Left Column: Login Form -->
    <div style="flex: 1; max-width: 400px;">
        <h2 style="margin-bottom: 1.5rem;">Log In</h2>

        <?php if (!empty($errors['general'])): ?>
            <div style="color: red; margin-bottom: 1rem;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/login">
    <?= \Core\CSRF::field() ?>
    
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

    <button type="submit" style="cursor: pointer;">
        Log In
    </button>
        </form>
        
        <p style="margin-top: 1rem;">
            Don't have an account? <a href="/auth/register">Register here</a>
        </p>
        <p style="margin-top: 0.5rem;">
            Forgot your password? <a href="/auth/forgot-password">Reset it</a>
        </p>
    </div>

    <!-- Right Column: Website Information -->
    <div style="flex: 1; padding: 2rem; background: rgba(255,255,255,0.1); border-radius: 8px;">
        <h3 style="margin-bottom: 1rem; color: #ffc0e0;">Welcome to WebSnap.com</h3>
        <p style="margin-bottom: 1rem; line-height: 1.8;">
            Share your creative moments with a vibrant community of photo enthusiasts.
        </p>
        <h4 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: #ffc0e0;">Features:</h4>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 0.5rem;">📸 Capture and edit photos</li>
            <li style="margin-bottom: 0.5rem;">✨ Add creative overlays</li>
            <li style="margin-bottom: 0.5rem;">❤️ Like and comment</li>
            <li style="margin-bottom: 0.5rem;">👥 Connect with others</li>
            <li style="margin-bottom: 0.5rem;">🎨 Express yourself</li>
        </ul>
    </div>
</div>