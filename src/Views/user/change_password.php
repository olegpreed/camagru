<?php
/** @var string $title */
/** @var array $user */
/** @var array $errors */
?>

<div class="profile-container">
    <div class="profile-layout">
        <div class="profile-main">
            <div class="profile-card">
                <h2>Update Your Password</h2>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/user/change-password">
                    <?php echo \Core\CSRF::field(); ?>

                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            autocomplete="current-password"
                            required
                            autofocus
                        >
                        <?php if (!empty($errors['current_password'])): ?>
                            <span class="form-error"><?php echo htmlspecialchars($errors['current_password']); ?></span>
                        <?php endif; ?>
                    </div>

                    <hr class="form-divider">

                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            autocomplete="new-password"
                            required
                        >
                        <?php if (!empty($errors['new_password'])): ?>
                            <span class="form-error"><?php echo htmlspecialchars($errors['new_password']); ?></span>
                        <?php endif; ?>
                        <small class="form-hint">
                            At least 8 characters with uppercase, lowercase, and numbers
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            autocomplete="new-password"
                            required
                        >
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <span class="form-error"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="/user/profile" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/profile.css">