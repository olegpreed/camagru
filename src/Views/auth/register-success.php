<div style="text-align: center; padding: 2rem;">
    <h2 style="color: green;">✅ Registration Successful!</h2>
    <p>We've sent a verification email to:</p>
    <p><strong><?= htmlspecialchars($email ?? '') ?></strong></p>
    <p>Please check your email and click the verification link to activate your account.</p>
    <p style="margin-top: 2rem;">
        <a href="/auth/login" style="color: #2c3e50;">Go to Login</a>
    </p>
</div>