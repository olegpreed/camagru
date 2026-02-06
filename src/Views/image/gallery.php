<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Image Gallery</h2>
        <?php 
        use Middleware\AuthMiddleware;
        $currentUser = AuthMiddleware::user();
        if ($currentUser): 
        ?>
            <a href="/image/upload" style="background: #2c3e50; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 5px;">
                Upload Image
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div style="background: #27ae60; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
        <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 8px;">
            <p style="font-size: 1.2rem; color: #666;">No images uploaded yet.</p>
            <?php if ($currentUser): ?>
                <p style="margin-top: 1rem;">
                    <a href="/image/upload" style="color: #2c3e50; text-decoration: underline;">
                        Be the first to upload!
                    </a>
                </p>
            <?php else: ?>
                <p style="margin-top: 1rem;">
                    <a href="/auth/login" style="color: #2c3e50; text-decoration: underline;">
                        Login to upload images
                    </a>
                </p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($images as $image): ?>
                <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;">
                    <div style="aspect-ratio: 1; overflow: hidden; background: #f0f0f0;">
                        <img 
                            src="/uploads/<?= htmlspecialchars($image['filename']) ?>" 
                            alt="<?= htmlspecialchars($image['original_filename'] ?? 'Image') ?>"
                            style="width: 100%; height: 100%; object-fit: cover;"
                        >
                    </div>
                    <div style="padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong style="color: #2c3e50;">
                                <?= htmlspecialchars($image['username']) ?>
                            </strong>
                            <span style="color: #7f8c8d; font-size: 0.875rem;">
                                <?= date('M j, Y', strtotime($image['created_at'])) ?>
                            </span>
                        </div>
                        <?php if (!empty($image['original_filename'])): ?>
                            <p style="color: #95a5a6; font-size: 0.875rem; margin-top: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($image['original_filename']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns"] {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
        gap: 1rem !important;
    }
}
</style>
