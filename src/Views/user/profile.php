<?php
/** @var string $title */
/** @var array $user */
/** @var bool $isEditing */
/** @var string $successMessage */
/** @var array $errors */
/** @var array $old */
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>My Profile</h1>
    </div>

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
                                required
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <span class="form-error"><?php echo htmlspecialchars($errors['email']); ?></span>
                            <?php endif; ?>
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
                            <span class="info-label">Account Status:</span>
                            <span class="info-value">
                                <?php echo $user['is_verified'] ? '<span class="badge badge-verified">Verified</span>' : '<span class="badge badge-unverified">Unverified</span>'; ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Member Since:</span>
                            <span class="info-value">
                                <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                            </span>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="/user/edit-profile" class="btn btn-primary">Edit Profile</a>
                        <a href="/user/change-password" class="btn btn-secondary">Change Password</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Quick Links -->
        <aside class="profile-sidebar">
            <div class="sidebar-card">
                <h3>Account Security</h3>
                <ul>
                    <li><a href="/user/change-password">Change Password</a></li>
                    <li><a href="/auth/logout">Log Out</a></li>
                </ul>
            </div>

            <div class="sidebar-card">
                <h3>Other</h3>
                <ul>
                    <li><a href="/">Back to Home</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .profile-header {
        margin-bottom: 30px;
    }

    .profile-header h1 {
        margin: 0;
        color: #333;
        font-size: 32px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 30px;
    }

    @media (max-width: 768px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-card h2 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
        font-size: 20px;
    }

    .profile-info {
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #666;
        font-weight: 600;
        min-width: 120px;
    }

    .info-value {
        color: #333;
        text-align: right;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-verified {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-unverified {
        background-color: #fff3cd;
        color: #856404;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
    }

    .form-group input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-hint {
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

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .profile-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e9ecef;
        color: #333;
    }

    .btn-secondary:hover {
        background: #dee2e6;
    }

    .profile-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sidebar-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .sidebar-card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #333;
        font-size: 16px;
    }

    .sidebar-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-card li {
        margin-bottom: 10px;
    }

    .sidebar-card li:last-child {
        margin-bottom: 0;
    }

    .sidebar-card a {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .sidebar-card a:hover {
        color: #764ba2;
        text-decoration: underline;
    }
</style>
