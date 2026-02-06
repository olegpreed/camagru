<div style="max-width: 600px; margin: 0 auto;">
    <h2>Upload Image</h2>
    
    <?php if (!empty($errors['general'])): ?>
        <div style="background: #e74c3c; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div style="background: #f8f9fa; padding: 2rem; border-radius: 8px; margin-top: 1rem;">
        <form action="/image/upload" method="POST" enctype="multipart/form-data">
            <?= \Core\CSRF::field() ?>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="image" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">
                    Choose Image
                </label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/jpeg,image/png,image/gif"
                    required
                    style="display: block; width: 100%; padding: 0.5rem; border: 2px solid #ddd; border-radius: 5px; background: white;"
                >
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Accepted formats: JPEG, PNG, GIF (Max size: 5 MB)
                </small>
            </div>

            <div style="margin-bottom: 1rem;">
                <img id="preview" src="" alt="" style="max-width: 100%; max-height: 300px; display: none; border-radius: 5px; margin-top: 1rem;">
            </div>

            <button 
                type="submit" 
                style="background: #2c3e50; color: white; padding: 0.75rem 2rem; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; width: 100%;"
            >
                Upload Image
            </button>
        </form>
    </div>

    <div style="margin-top: 2rem; text-align: center;">
        <a href="/gallery" style="color: #2c3e50; text-decoration: underline;">
            &larr; Back to Gallery
        </a>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
