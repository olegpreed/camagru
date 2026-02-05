<div style="text-align: center; padding: 2rem;">
    <h2 style="color: red;">❌ Verification Failed</h2>
    <p><?= htmlspecialchars($message ?? 'Invalid or expired verification link.') ?></p>
    <p style="margin-top: 2rem;">
        <a href="/auth/register" style="color: #2c3e50;">Register Again</a> | 
        <a href="/auth/login" style="color: #2c3e50;">Log In</a>
    </p>
</div>