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

    <div id="gallery-container" style="display: flex; flex-direction: column; gap: 2rem; max-width: 50vh; margin: 0 auto;">
        <!-- Images will be loaded here via JavaScript -->
    </div>
    <div id="empty-gallery" style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 8px; display: none;">
        <p style="font-size: 1.2rem; color: #666;">No images uploaded yet.</p>
        <?php if ($currentUser): ?>
            <p style="margin-top: 1rem;">
                <a href="/image/edit" style="color: #2c3e50; text-decoration: underline;">
                    Be the first to create an image!
                </a>
            </p>
        <?php else: ?>
            <p style="margin-top: 1rem;">
                <a href="/auth/login" style="color: #2c3e50; text-decoration: underline;">
                    Login to create images
                </a>
            </p>
        <?php endif; ?>
    </div>
    <div id="loading-indicator" style="text-align: center; padding: 2rem; display: none;">
        <div class="spinner"></div>
    </div>
    <div id="no-more-images" style="text-align: center; padding: 2rem; color: #999; display: none;">
        <p>No more images to load</p>
    </div>
</div>

<style>
    .gallery-item {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .gallery-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .gallery-image-container {
        width: 100%;
        aspect-ratio: 1;
        background: #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .gallery-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gallery-item-info {
        padding: 1rem;
    }

    .gallery-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .gallery-username {
        color: #2c3e50;
        font-weight: bold;
    }

    .gallery-date {
        color: #7f8c8d;
        font-size: 0.875rem;
    }

    .gallery-filename {
        color: #95a5a6;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        #gallery-container {
            max-width: 90vw !important;
        }
    }
</style>

<script>
    let currentOffset = 0;
    let isLoading = false;
    let hasMoreImages = true;
    const initialLimit = 5;
    const batchLimit = 5;

    const galleryContainer = document.getElementById('gallery-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    const noMoreImages = document.getElementById('no-more-images');
    const emptyGallery = document.getElementById('empty-gallery');

    // Load images from server
    async function loadImages(limit, offset) {
        if (isLoading || !hasMoreImages) return;

        isLoading = true;
        loadingIndicator.style.display = 'block';

        try {
            const response = await fetch(`/image/getImages?limit=${limit}&offset=${offset}`);
            const data = await response.json();

            // TEMPORARY: Add delay to see loading behavior (remove in production)
            // await new Promise(resolve => setTimeout(resolve, 2000));

            if (data.success && data.images.length > 0) {
                renderImages(data.images);
                currentOffset += data.images.length;

                // Check if there are more images
                if (data.images.length < limit) {
                    hasMoreImages = false;
                    noMoreImages.style.display = 'block';
                }
            } else {
                hasMoreImages = false;
                // Show empty gallery message only on first load
                if (offset === 0) {
                    emptyGallery.style.display = 'block';
                } else {
                    noMoreImages.style.display = 'block';
                }
            }
        } catch (error) {
            console.error('Failed to load images:', error);
        } finally {
            isLoading = false;
            loadingIndicator.style.display = 'none';
        }
    }

    // Render images to the gallery
    function renderImages(images) {
        images.forEach(image => {
            const imageElement = createImageElement(image);
            galleryContainer.appendChild(imageElement);
        });
    }

    // Create image element
    function createImageElement(image) {
        const div = document.createElement('div');
        div.className = 'gallery-item';
        
        const date = new Date(image.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });

        div.innerHTML = `
            <div class="gallery-image-container">
                <img 
                    src="/uploads/images/${image.filename}" 
                    alt="${image.original_filename || 'Image'}"
                    loading="lazy"
                >
            </div>
            <div class="gallery-item-info">
                <div class="gallery-item-header">
                    <span class="gallery-username">${image.username}</span>
                    <span class="gallery-date">${formattedDate}</span>
                </div>
                ${image.original_filename ? `<p class="gallery-filename">${image.original_filename}</p>` : ''}
            </div>
        `;

        return div;
    }

    // Infinite scroll detection
    function handleScroll() {
        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;

        // Load more when user is 300px from bottom
        if (scrollPosition >= pageHeight - 300 && !isLoading && hasMoreImages) {
            loadImages(batchLimit, currentOffset);
        }
    }

    // Initialize
    window.addEventListener('scroll', handleScroll);
    
    // Load initial batch
    if (galleryContainer) {
        loadImages(initialLimit, 0);
    }
</script>
