<div class="auth-container">
    <!-- Left Column: Login Form -->
    <div class="auth-form-column">
        <h2>Log In</h2>

        <?php if (!empty($errors['general'])): ?>
            <div class="auth-general-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/login">
            <?= \Core\CSRF::field() ?>
            
            <div class="auth-form-group">
                <label for="login">Username or Email:</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    value="<?= htmlspecialchars($old['login'] ?? '') ?>"
                    required
                >
                <?php if (!empty($errors['login'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['login']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="auth-form-group">
                <label for="password">Password:</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
                <?php if (!empty($errors['password'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['password']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit">
                Log In
            </button>
        </form>
        
        <div class="auth-form-links">
            <p>
                Don't have an account? <a href="/auth/register">Register here</a>
            </p>
            <p>
                Forgot your password? <a href="/auth/forgot-password">Reset it</a>
            </p>
        </div>
    </div>

    <!-- Right Column: Website Information -->
    <div class="auth-info-column">
        <img src="/assets/images/people.png" alt="Community" class="auth-info-image">
        <h3>Welcome to WebSnap.com</h3>
        <p>
            Join our creative community and share your moments with photo enthusiasts from around the world.
        </p>
        <h4>Features:</h4>
        <ul>
            <li>Capture and edit photos</li>
            <li>Add creative overlays</li>
            <li>Like and comment on photos</li>
            <li>Connect with other creators</li>
            <li>Express yourself</li>
        </ul>
    </div>
</div>