<div class="auth-container">
    <!-- Left Column: Register Form -->
    <div class="auth-form-column">
        <h2>Create an Account</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="auth-general-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/register">
            <?= \Core\CSRF::field() ?>
            
            <div class="auth-form-group">
                <label for="username">Username:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['username'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['username']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="auth-form-group">
                <label for="email">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['email']) ?>
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
                <?php if (isset($errors['password'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['password']) ?>
                    </div>
                <?php endif; ?>
                <span class="field-help">
                    Must be at least 8 characters with uppercase, lowercase, and number
                </span>
            </div>

            <div class="auth-form-group">
                <label for="password_confirm">Confirm Password:</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    required
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="field-error">
                        <?= htmlspecialchars($errors['password_confirm']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit">
                Register
            </button>
        </form>
        
        <div class="auth-form-links">
            <p>
                Already have an account? <a href="/auth/login">Log in here</a>
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