<?php
/** @var string $title */
/** @var array $user */
/** @var array $errors */
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Change Password</h1>
    </div>

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

        <aside class="profile-sidebar">
            <div class="sidebar-card">
                <h3>Security Tips</h3>
                <ul class="tips-list">
                    <li>Use a unique password</li>
                    <li>Avoid common words and dates</li>
                    <li>Mix uppercase and lowercase</li>
                    <li>Include numbers and symbols</li>
                    <li>Keep it at least 8 characters</li>
                </ul>
            </div>

            <div class="sidebar-card">
                <h3>Account</h3>
                <ul>
                    <li><a href="/user/profile">Back to Profile</a></li>
                    <li><a href="/auth/logout">Log Out</a></li>
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

    .form-divider {
        border: none;
        border-top: 1px solid #eee;
        margin: 20px 0;
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

    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        padding: 8px 0;
        color: #666;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
    }

    .tips-list li:last-child {
        border-bottom: none;
    }

    .tips-list li:before {
        content: "✓ ";
        color: #28a745;
        font-weight: bold;
        margin-right: 8px;
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
