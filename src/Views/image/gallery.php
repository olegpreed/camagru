<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <?php 
        use Middleware\AuthMiddleware;
        $currentUser = AuthMiddleware::user();
        if ($currentUser): 
        ?>
        <?php endif; ?>
    </div>

    <div id="gallery-container" style="display: flex; flex-direction: column; gap: 2rem; margin: 0 auto;">
        <!-- Gallery items will be loaded here -->
    </div>

    <div id="empty-gallery" style="text-align: center; padding: 3rem; display: none;">
        <p style="font-size: 1.2rem; color: #666;">No images uploaded yet ;(</p>
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

    <!-- Lightbox for full image view -->
    <div id="lightbox" class="lightbox hidden" aria-hidden="true">
        <div class="lightbox-backdrop" id="lightbox-backdrop"></div>
        <div class="lightbox-container">
            <button type="button" class="lightbox-close" id="lightbox-close">&times;</button>
            <img id="lightbox-image" alt="Full view image">
        </div>
    </div>
</div>

<style>
    .gallery-item-wrapper {
        background: rgba(255, 255, 255, 0.5);
        overflow: hidden;
		border: 1px solid rgb(0, 0, 0);
		max-width: 900px;
		aspect-ratio: 2 / 1;
    }

    .gallery-item-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        height: 100%;
    }

    .gallery-image-section {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
		/* border: 1px solid rgb(0, 0, 0); */
		padding: 1rem;
		/* background: rgb(229, 229, 229); */
		width: 100%;
		/* box-shadow: 2px 2px 1px rgba(0,0,0,0.4); */
        aspect-ratio: 1;
    }

    .gallery-image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
    }

    .gallery-info-section {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
		min-height: 0;
		background: linear-gradient(90deg, transparent, rgba(208, 224, 255, 0.53) 80%);
    }

    .gallery-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .gallery-user-info {
        display: flex;
        flex-direction: column;
    }

    .gallery-username {
        font-weight: bold;
        color: #004fc5;
    }

    .gallery-date {
        color: #bfbfbf;
    }

    .gallery-actions {
        display: none;
    }

    /* Like button on image - entire badge is clickable */
    .image-like-badge {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 10;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .image-like-badge:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .image-like-badge.active {
        background: rgba(52, 152, 219, 0.95);
    }

    .image-like-badge.active .image-like-count {
        color: white;
    }

    .image-like-badge img {
        transition: transform 0.2s;
    }

    .image-like-badge:hover:not(:disabled) img {
        transform: translateY(-2px);
    }

    .image-like-count {
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 20px;
        color: #2c3e50;
    }



    .stat-count {
        display: none;
    }

    .comments-section {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

    .comments-list {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding-right: 0.5rem;
		min-height: 0;
    }

    .comment-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        /* padding: 0.5rem 0; */
    }

    .comment-header {
        font-weight: 600;
        color: #959595;
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

    .comments-empty {
        color: #999;
        font-style: italic;
        padding: 0.5rem 0;
    }

    .comment-form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        border-top: 1px solid #eee;
        padding-top: 0.75rem;
    }

    .comment-form textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 0.9rem;
        resize: vertical;
        max-height: 80px;
    }

    .comment-form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
    }

    .comment-remaining {
        color: #999;
        font-size: 0.8rem;
    }

    .comment-btn {
        padding: 0.2rem 0.5rem;
        cursor: pointer;
    }

    .comment-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .login-hint {
        color: #999;
        font-size: 0.85rem;
        text-align: center;
        padding: 0.75rem;
        background: #f9f9f9;
        border-radius: 4px;
    }

    /* Lightbox styles */
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .lightbox.hidden {
        display: none;
    }

    .lightbox-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
    }

    .lightbox-container {
        position: relative;
        z-index: 1001;
        max-width: 90%;
        max-height: 90%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1002;
    }

    .lightbox-close:hover {
        background: #f0f0f0;
    }

    #lightbox-image {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .gallery-item-wrapper {
            aspect-ratio: auto;
        }

        .gallery-item-content {
            grid-template-columns: 1fr;
            min-height: auto;
            max-height: none;
        }

        .gallery-image-section {
            aspect-ratio: 1;
            min-height: 300px;
        }

        .gallery-info-section {
            min-height: 400px;
        }

        .lightbox-container {
            max-width: 95%;
        }
    }

    @media (max-width: 480px) {
        .gallery-item-wrapper {
            border-radius: 0;
        }

        .gallery-info-section {
            padding: 1rem;
            min-height: 350px;
        }

        .gallery-actions {
            flex-wrap: wrap;
        }

        .like-btn {
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        .comment-form textarea {
            font-size: 16px;
        }
    }
</style>

<script>
    const isLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
    const galleryContainer = document.getElementById('gallery-container');
    const emptyGallery = document.getElementById('empty-gallery');
    const loadingIndicator = document.getElementById('loading-indicator');
    const noMoreImages = document.getElementById('no-more-images');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxBackdrop = document.getElementById('lightbox-backdrop');

    let currentOffset = 0;
    let isLoading = false;
    let hasMoreImages = true;
    const initialLimit = 10;
    const batchLimit = 10;

    async function fetchCsrfToken() {
        const response = await fetch('/image/getCsrfToken');
        if (!response.ok) {
            throw new Error('Failed to get CSRF token');
        }
        const data = await response.json();
        return data.csrf_token;
    }

    async function loadImageComments(imageId, commentsList) {
        try {
            const response = await fetch(`/image/getImageDetails?id=${imageId}`);
            const data = await response.json();

            if (data.success) {
                renderComments(commentsList, data.comments);
            }
        } catch (error) {
            console.error('Failed to load comments:', error);
        }
    }

    async function toggleLike(imageId, button, likeCountSpan, badge) {
        if (!isLoggedIn) return;

        try {
            const csrfToken = await fetchCsrfToken();
            const formData = new FormData();
            formData.append('image_id', imageId);
            formData.append('csrf_token', csrfToken);

            const response = await fetch('/image/toggleLike', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to toggle like');
            }

            // Set the active state based on server response
            if (data.liked) {
                button.classList.add('active');
                badge.classList.add('active');
            } else {
                button.classList.remove('active');
                badge.classList.remove('active');
            }
            likeCountSpan.textContent = data.count;
        } catch (error) {
            console.error('Like error:', error);
        }
    }

    async function addComment(imageId, content, textarea, remainingSpan, commentsContainer, likeBtn, commentCountSpan) {
        if (!isLoggedIn || !content.trim()) return;

        try {
            const csrfToken = await fetchCsrfToken();
            const formData = new FormData();
            formData.append('image_id', imageId);
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

            textarea.value = '';
            remainingSpan.textContent = '500';
            
            renderComments(commentsContainer, data.comments);
        } catch (error) {
            console.error('Comment error:', error);
        }
    }

    function renderComments(container, comments) {
        container.innerHTML = '';

        if (!comments || comments.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'comments-empty';
            emptyDiv.textContent = 'No comments yet.';
            container.appendChild(emptyDiv);
            return;
        }

        comments.forEach(comment => {
            const date = new Date(comment.created_at);
            const formattedDate = date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            const commentItem = document.createElement('div');
            commentItem.className = 'comment-item';

            const headerDiv = document.createElement('div');
            headerDiv.className = 'comment-header';
            headerDiv.textContent = comment.username + ':';

            // const dateDiv = document.createElement('div');
            // dateDiv.className = 'comment-date';
            // dateDiv.textContent = formattedDate;

            const contentDiv = document.createElement('div');
            contentDiv.className = 'comment-content';
            contentDiv.textContent = comment.content;

            commentItem.appendChild(headerDiv);
            // commentItem.appendChild(dateDiv);
            commentItem.appendChild(contentDiv);

            container.appendChild(commentItem);
        });
    }

    function createImageElement(image) {
        const wrapper = document.createElement('div');
        wrapper.className = 'gallery-item-wrapper';

        const content = document.createElement('div');
        content.className = 'gallery-item-content';

        // Image section
        const imageSection = document.createElement('div');
        imageSection.className = 'gallery-image-section';

        const img = document.createElement('img');
        img.src = '/uploads/images/' + image.filename;
        img.alt = image.original_filename || 'Image';
        img.loading = 'lazy';
        imageSection.appendChild(img);

        img.addEventListener('click', () => {
            lightboxImage.src = img.src;
            lightbox.classList.remove('hidden');
            lightbox.setAttribute('aria-hidden', 'false');
        });

        // Like badge on image (entire badge is clickable)
        const likeBadge = document.createElement('button');
        likeBadge.className = 'image-like-badge' + (image.liked_by_user ? ' active' : '');
        likeBadge.type = 'button';
        likeBadge.disabled = !isLoggedIn;

        const likeCount = document.createElement('span');
        likeCount.className = 'image-like-count';
        likeCount.textContent = image.like_count ?? 0;

        const thumbImg = document.createElement('img');
        thumbImg.src = '/assets/images/thumbs.png';
        thumbImg.alt = 'Like';
        thumbImg.style.width = '20px';
        thumbImg.style.height = '20px';

        likeBadge.appendChild(likeCount);
        likeBadge.appendChild(thumbImg);

        likeBadge.addEventListener('click', () => {
            toggleLike(image.id, likeBadge, likeCount, likeBadge);
        });

        // Add like badge to image section
        imageSection.appendChild(likeBadge);

        // Info section
        const infoSection = document.createElement('div');
        infoSection.className = 'gallery-info-section';

        // Header
        const header = document.createElement('div');
        header.className = 'gallery-header';

        const userInfo = document.createElement('div');
        userInfo.className = 'gallery-user-info';

        const username = document.createElement('div');
        username.className = 'gallery-username';
        username.textContent = image.username;

        const date = new Date(image.created_at);
        const formattedDate = date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const dateDiv = document.createElement('div');
        dateDiv.className = 'gallery-date';
        dateDiv.textContent = formattedDate;

        userInfo.appendChild(username);
        userInfo.appendChild(dateDiv);
        header.appendChild(userInfo);

        // Actions
        const actions = document.createElement('div');
        actions.className = 'gallery-actions';

        // Comments section
        const commentsSection = document.createElement('div');
        commentsSection.className = 'comments-section';

        const commentsList = document.createElement('div');
        commentsList.className = 'comments-list';

        renderComments(commentsList, image.comments || []);
        loadImageComments(image.id, commentsList);

        // Comment form
        const form = document.createElement('div');
        form.className = 'comment-form';

        const textarea = document.createElement('textarea');
        textarea.rows = 2;
        textarea.maxLength = 500;
        textarea.placeholder = 'Write a comment...';
        textarea.disabled = !isLoggedIn;

        const formFooter = document.createElement('div');
        formFooter.className = 'comment-form-footer';

        const remaining = document.createElement('span');
        remaining.className = 'comment-remaining';
        remaining.textContent = '500';

        const submitBtn = document.createElement('button');
        submitBtn.className = 'comment-btn';
        submitBtn.textContent = 'Post';
        submitBtn.type = 'button';
        submitBtn.disabled = !isLoggedIn;

        textarea.addEventListener('input', () => {
            remaining.textContent = String(500 - textarea.value.length);
        });

        submitBtn.addEventListener('click', () => {
            addComment(image.id, textarea.value, textarea, remaining, commentsList, likeBtn);
        });

        let loginHint = null;
        if (!isLoggedIn) {
            loginHint = document.createElement('div');
            loginHint.className = 'login-hint';
            loginHint.textContent = 'Log in to like or comment.';
            form.appendChild(loginHint);
        } else {
            formFooter.appendChild(remaining);
            formFooter.appendChild(submitBtn);
            form.appendChild(textarea);
            form.appendChild(formFooter);
        }

        commentsSection.appendChild(commentsList);
        if (!isLoggedIn) {
            commentsSection.appendChild(loginHint);
        } else {
            commentsSection.appendChild(form);
        }

        infoSection.appendChild(header);
        infoSection.appendChild(commentsSection);

        content.appendChild(imageSection);
        content.appendChild(infoSection);
        wrapper.appendChild(content);

        return wrapper;
    }

    async function loadImages(limit, offset) {
        if (isLoading || !hasMoreImages) return;

        isLoading = true;
        loadingIndicator.style.display = 'block';

        try {
            const response = await fetch(`/image/getImages?limit=${limit}&offset=${offset}`);
            const data = await response.json();

            if (data.success && data.images.length > 0) {
                data.images.forEach(image => {
                    const element = createImageElement(image);
                    galleryContainer.appendChild(element);
                });

                currentOffset += data.images.length;

                if (data.images.length < limit) {
                    hasMoreImages = false;
                    noMoreImages.style.display = 'block';
                }
            } else {
                hasMoreImages = false;
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

    function handleScroll() {
        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;

        if (scrollPosition >= pageHeight - 300 && !isLoading && hasMoreImages) {
            loadImages(batchLimit, currentOffset);
        }
    }

    // Lightbox controls
    function closeLightbox() {
        lightbox.classList.add('hidden');
        lightbox.setAttribute('aria-hidden', 'true');
    }

    lightboxClose.addEventListener('click', closeLightbox);
    lightboxBackdrop.addEventListener('click', closeLightbox);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
            closeLightbox();
        }
    });

    // Initialize
    window.addEventListener('scroll', handleScroll);
    if (galleryContainer) {
        loadImages(initialLimit, 0);
    }
</script>
