<div style="text-align: center; padding: 2rem;">
    <h2 style="color: green;">✅ Email Verified!</h2>
    <p><?= htmlspecialchars($message ?? 'Your email has been verified successfully!') ?></p>
    <p style="margin-top: 2rem;">
        <a href="/auth/login" style="display: inline-block; padding: 0.75rem 1.5rem; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px;">
            Log In
        </a>
    </p>
</div>