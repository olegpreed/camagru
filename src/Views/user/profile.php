<?php
/** @var string $title */
/** @var array $user */
/** @var bool $isEditing */
/** @var string $successMessage */
/** @var array $errors */
/** @var array $old */
?>

<div class="profile-container">
    <?php if (!empty($_GET['updated'])): ?>
        <div class="alert alert-success">
            Your profile has been updated successfully!
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['password_changed'])): ?>
        <div class="alert alert-success">
            Your password has been changed successfully!
        </div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($errors['general']); ?>
        </div>
    <?php endif; ?>

    <div class="profile-layout">
        <!-- Main Profile Section -->
        <div class="profile-main">
            <?php if (!empty($isEditing)): ?>
                <!-- Edit Profile Form -->
                <div class="profile-card">
                    <h2>Edit Profile</h2>
                    <form method="POST" action="/user/edit-profile">
                        <?php echo \Core\CSRF::field(); ?>

                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="<?php echo htmlspecialchars($old['username'] ?? $user['username']); ?>" 
                                autocomplete="username"
                                required
                            >
                            <?php if (!empty($errors['username'])): ?>
                                <span class="form-error"><?php echo htmlspecialchars($errors['username']); ?></span>
                            <?php endif; ?>
                            <small class="form-hint">3-50 characters, letters, numbers, and underscores only</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($old['email'] ?? $user['email']); ?>" 
                                autocomplete="email"
                                required
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <span class="form-error"><?php echo htmlspecialchars($errors['email']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>
                                <span>Email me when I get a comment</span>
                                <input 
                                    type="checkbox" 
                                    name="comment_notifications" 
                                    value="1"
                                    <?php
                                        $notifications = $old['comment_notifications'] ?? $user['comment_notifications'];
                                        echo ((int)$notifications === 1) ? 'checked' : '';
                                    ?>
                                >
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="/user/profile" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- View Profile -->
                <div class="profile-card">
                    <h2>Account Information</h2>
                    <div class="profile-info">
                        <div class="info-row">
                            <span class="info-label">Username:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Member Since:</span>
                            <span class="info-value">
                                <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Comment Notifications:</span>
                            <span class="info-value">
                                <?php echo ((int)$user['comment_notifications'] === 1) ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="button" onclick="window.location.href='/user/edit-profile'" class="btn btn-primary">Edit Profile</button>
                        <button type="button" onclick="window.location.href='/user/change-password'" class="btn btn-secondary">Change Password</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/profile.css">
