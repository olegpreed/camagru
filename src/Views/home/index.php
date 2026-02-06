<div style="text-align: center; padding: 2rem 0;">
    <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: #2c3e50;">
        Welcome to Camagru
    </h2>
    <p style="font-size: 1.2rem; color: #7f8c8d; margin-bottom: 2rem;">
        Share your photos with the world!
    </p>
    
    <?php
    use Middleware\AuthMiddleware;
    $user = AuthMiddleware::user();
    ?>
    
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="/gallery" style="display: inline-block; background: #2c3e50; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 5px; font-size: 1.1rem;">
            View Gallery
        </a>
        
        <?php if ($user): ?>
            <a href="/image/upload" style="display: inline-block; background: #27ae60; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 5px; font-size: 1.1rem;">
                Upload Image
            </a>
        <?php else: ?>
            <a href="/auth/register" style="display: inline-block; background: #27ae60; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 5px; font-size: 1.1rem;">
                Get Started
            </a>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 3rem; padding: 2rem; background: #ecf0f1; border-radius: 8px;">
    <h3 style="margin-bottom: 1rem; color: #2c3e50;">Features</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="padding: 0.5rem 0; color: #34495e;">📸 Upload and share your photos</li>
        <li style="padding: 0.5rem 0; color: #34495e;">🎨 Apply fun filters and effects (coming soon)</li>
        <li style="padding: 0.5rem 0; color: #34495e;">💬 Comment and like photos (coming soon)</li>
        <li style="padding: 0.5rem 0; color: #34495e;">👥 Connect with other users</li>
    </ul>
</div>