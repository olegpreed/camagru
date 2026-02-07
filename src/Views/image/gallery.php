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

    <div id="image-modal" class="modal hidden" aria-hidden="true">
        <div class="modal-backdrop" id="modal-backdrop"></div>
        <div class="modal-content" role="dialog" aria-modal="true">
            <button type="button" class="modal-close" id="modal-close">&times;</button>
            <div class="modal-body">
                <div class="modal-image">
                    <img id="modal-image" alt="Full image">
                </div>
                <div class="modal-side">
                    <div class="modal-header">
                        <div>
                            <div id="modal-username" class="modal-username"></div>
                            <div id="modal-date" class="modal-date"></div>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" id="modal-like-btn" class="like-btn">❤️ Like</button>
                        <span id="modal-like-count" class="modal-stat"></span>
                        <span id="modal-comment-count" class="modal-stat"></span>
                    </div>
                    <div id="modal-comments" class="modal-comments"></div>
                    <div id="modal-comment-form" class="modal-comment-form">
                        <textarea id="modal-comment-input" rows="3" maxlength="500" placeholder="Write a comment..."></textarea>
                        <div class="modal-comment-footer">
                            <span id="modal-comment-remaining" class="modal-remaining">500</span>
                            <button type="button" id="modal-comment-submit" class="comment-btn">Post</button>
                        </div>
                        <div id="modal-comment-login" class="modal-login-hint" style="display: none;">
                            Log in to like or comment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    .gallery-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        color: #5f6b77;
        font-size: 0.9rem;
        font-weight: 600;
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

    .modal.hidden {
        display: none;
    }

    .modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        position: relative;
        background: white;
        width: min(1000px, 90vw);
        max-height: 90vh;
        border-radius: 10px;
        overflow: hidden;
        display: grid;
        grid-template-rows: 1fr;
        z-index: 1;
    }

    .modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        font-size: 20px;
        cursor: pointer;
        z-index: 2;
    }

    .modal-body {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        max-height: 90vh;
    }

    .modal-image {
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-image img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        background: #111;
    }

    .modal-side {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow: hidden;
    }

    .modal-username {
        font-weight: 700;
        color: #2c3e50;
    }

    .modal-date {
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    .modal-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .like-btn {
        background: #f1f3f5;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        font-weight: 600;
    }

    .like-btn.active {
        background: #ffe3e3;
        color: #c92a2a;
    }

    .like-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-stat {
        color: #5f6b77;
        font-weight: 600;
    }

    .modal-comments {
        flex: 1;
        overflow-y: auto;
        border-top: 1px solid #eee;
        padding-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .comment-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .comment-header {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }

    .comment-date {
        color: #9aa3ab;
        font-size: 0.75rem;
    }

    .comment-content {
        color: #4b5563;
        font-size: 0.9rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .modal-comment-form {
        border-top: 1px solid #eee;
        padding-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-comment-form textarea {
        width: 100%;
        resize: vertical;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 8px;
        font-size: 0.9rem;
    }

    .modal-comment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .comment-btn {
        background: #2c3e50;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
    }

    .comment-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-remaining {
        color: #9aa3ab;
        font-size: 0.85rem;
    }

    .modal-login-hint {
        color: #9aa3ab;
        font-size: 0.85rem;
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

        .modal-body {
            grid-template-columns: 1fr;
        }

        .modal-image img {
            max-height: 60vh;
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

    const isLoggedIn = <?php echo $currentUser ? 'true' : 'false'; ?>;
    const currentUserId = <?php echo $currentUser ? (int)$currentUser['id'] : 'null'; ?>;

    const modal = document.getElementById('image-modal');
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalClose = document.getElementById('modal-close');
    const modalImage = document.getElementById('modal-image');
    const modalUsername = document.getElementById('modal-username');
    const modalDate = document.getElementById('modal-date');
    const modalLikeBtn = document.getElementById('modal-like-btn');
    const modalLikeCount = document.getElementById('modal-like-count');
    const modalCommentCount = document.getElementById('modal-comment-count');
    const modalComments = document.getElementById('modal-comments');
    const modalCommentForm = document.getElementById('modal-comment-form');
    const modalCommentInput = document.getElementById('modal-comment-input');
    const modalCommentSubmit = document.getElementById('modal-comment-submit');
    const modalCommentRemaining = document.getElementById('modal-comment-remaining');
    const modalCommentLogin = document.getElementById('modal-comment-login');

    let activeImageId = null;

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

    async function fetchCsrfToken() {
        const response = await fetch('/image/getCsrfToken');
        if (!response.ok) {
            throw new Error('Failed to get CSRF token');
        }
        const data = await response.json();
        return data.csrf_token;
    }

    function updateGalleryCounts(imageId, likeCount, commentCount) {
        const item = galleryContainer.querySelector(`[data-image-id="${imageId}"]`);
        if (!item) return;
        const likeSpan = item.querySelector('.stat-like');
        const commentSpan = item.querySelector('.stat-comment');
        if (likeSpan) {
            likeSpan.textContent = `❤️ ${likeCount}`;
        }
        if (commentSpan) {
            commentSpan.textContent = `💬 ${commentCount}`;
        }
    }

    function renderComments(comments) {
        if (!comments || comments.length === 0) {
            modalComments.innerHTML = '<div class="comment-content">No comments yet.</div>';
            return;
        }

        modalComments.innerHTML = comments.map(comment => {
            const date = new Date(comment.created_at);
            const formattedDate = date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            return `
                <div class="comment-item">
                    <div class="comment-header">${comment.username}</div>
                    <div class="comment-date">${formattedDate}</div>
                    <div class="comment-content">${comment.content}</div>
                </div>
            `;
        }).join('');
    }

    async function openModal(imageId) {
        activeImageId = imageId;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');

        try {
            const response = await fetch(`/image/getImageDetails?id=${imageId}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to load image');
            }

            const image = data.image;
            const date = new Date(image.created_at);
            const formattedDate = date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            modalImage.src = `/uploads/images/${image.filename}`;
            modalUsername.textContent = image.username;
            modalDate.textContent = formattedDate;
            modalLikeCount.textContent = `❤️ ${data.like_count}`;
            modalCommentCount.textContent = `💬 ${data.comment_count}`;

            if (data.liked_by_user) {
                modalLikeBtn.classList.add('active');
            } else {
                modalLikeBtn.classList.remove('active');
            }

            modalLikeBtn.disabled = !isLoggedIn;
            modalCommentInput.value = '';
            modalCommentRemaining.textContent = '500';

            if (isLoggedIn) {
                modalCommentLogin.style.display = 'none';
                modalCommentInput.disabled = false;
                modalCommentSubmit.disabled = false;
            } else {
                modalCommentLogin.style.display = 'block';
                modalCommentInput.disabled = true;
                modalCommentSubmit.disabled = true;
            }

            renderComments(data.comments);
        } catch (error) {
            console.error('Failed to load image details:', error);
            closeModal();
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        activeImageId = null;
    }

    modalClose.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    modalLikeBtn.addEventListener('click', async () => {
        if (!isLoggedIn || !activeImageId) return;

        try {
            const csrfToken = await fetchCsrfToken();
            const formData = new FormData();
            formData.append('image_id', activeImageId);
            formData.append('csrf_token', csrfToken);

            const response = await fetch('/image/toggleLike', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to toggle like');
            }

            modalLikeCount.textContent = `❤️ ${data.count}`;
            updateGalleryCounts(activeImageId, data.count, parseInt(modalCommentCount.textContent.replace('💬', '').trim(), 10));
            if (data.liked) {
                modalLikeBtn.classList.add('active');
            } else {
                modalLikeBtn.classList.remove('active');
            }
        } catch (error) {
            console.error('Like error:', error);
        }
    });

    modalCommentInput.addEventListener('input', () => {
        const remaining = 500 - modalCommentInput.value.length;
        modalCommentRemaining.textContent = String(remaining);
    });

    modalCommentSubmit.addEventListener('click', async () => {
        if (!isLoggedIn || !activeImageId) return;

        const content = modalCommentInput.value.trim();
        if (content.length === 0 || content.length > 500) {
            return;
        }

        try {
            const csrfToken = await fetchCsrfToken();
            const formData = new FormData();
            formData.append('image_id', activeImageId);
            formData.append('content', content);
            formData.append('csrf_token', csrfToken);

            const response = await fetch('/image/addComment', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to add comment');
            }

            modalCommentInput.value = '';
            modalCommentRemaining.textContent = '500';
            modalCommentCount.textContent = `💬 ${data.comment_count}`;
            renderComments(data.comments);

            updateGalleryCounts(activeImageId, parseInt(modalLikeCount.textContent.replace('❤️', '').trim(), 10), data.comment_count);
        } catch (error) {
            console.error('Comment error:', error);
        }
    });

    // Create image element
    function createImageElement(image) {
        const div = document.createElement('div');
        div.className = 'gallery-item';
        div.dataset.imageId = image.id;
        
        const likeCount = image.like_count ?? 0;
        const commentCount = image.comment_count ?? 0;

        div.innerHTML = `
            <div class="gallery-image-container">
                <img 
                    src="/uploads/images/${image.filename}" 
                    alt="${image.original_filename || 'Image'}"
                    loading="lazy"
                >
            </div>
            <div class="gallery-stats">
                <span class="stat-like">❤️ ${likeCount}</span>
                <span class="stat-comment">💬 ${commentCount}</span>
            </div>
        `;

        const imageContainer = div.querySelector('.gallery-image-container');
        imageContainer.addEventListener('click', () => openModal(image.id));

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
