<style>
    .edit-container {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 2rem;
        max-width: 900px;
        margin: 0 auto;
        height: 100%;
        overflow: hidden;
    }

    .main-section {
        padding: 1rem 0;
        overflow-y: auto;
        min-height: 0;
        direction: rtl;
    }
    
    .main-section > * {
        direction: ltr;
    }

    .webcam-area {
        background: white;
        border: 1px solid #000000;
        padding: 2rem;
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .webcam-area.hidden {
        display: none;
    }

    #video-stream {
        width: 100%;
        max-width: 600px;
        /* background: #000; */
        margin-bottom: 1rem;
    }

    .webcam-controls {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .capture-photo-btn {
        padding: 0.75rem 2rem;
		font-weight: bold;
    }

    .capture-photo-btn:disabled {
        cursor: not-allowed;
    }

    .toggle-upload-btn {
        padding: 0.75rem 2rem;
    }

    .camera-permission-error {
        color: #ff1a31;
        padding: 1rem;
        margin-bottom: 1rem;
        text-align: center;
    }

    .upload-area {
        background: white;
        border: 1px dashed black;
        padding: 3rem;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-area.hidden {
        display: none;
    }

    .upload-area.dragover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    #preview-image {
        max-width: 100%;
        max-height: 400px;
        display: none;
        margin-bottom: 1rem;
    }

    .upload-input {
        display: none;
    }

    .upload-btn {
		padding: 0.75rem 2rem;
    }

    .overlays-section {
        margin-bottom: 2rem;
        min-width: 0;
    }

    .overlays-section h3 {
        margin-bottom: 1rem;
        color: #2c3e50;
    }

    .overlays-grid {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 0.5rem;
        min-width: 0;
    }

    .overlay-item {
        position: relative;
        cursor: pointer;
        border: 3px solid transparent;
        padding: 0.5rem;
        text-align: center;
        flex-shrink: 0;
        min-width: 80px;
    }

    .overlay-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .overlay-item.selected {
        border: 2px solid #2add54;
    }

    .overlay-item input[type="radio"] {
        display: none;
    }

    .overlay-preview {
        width: 60px;
        height: 60px;
        /* background: #f8f9fa; */
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
    }

    .overlay-name {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .capture-btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: bold;
        width: 100%;
    }

    .capture-btn:disabled {
        cursor: not-allowed;
        /* opacity: 0.6; */
    }

    .sidebar {
        padding: 1rem;
		overflow-y: auto;
        min-height: 0;
    }

    .sidebar h3 {
        margin-bottom: 1rem;
        color: #2c3e50;
    }

    .thumbnails-grid {
        display: grid;
        gap: 1rem;
    }

    .thumbnail-item {
        position: relative;
        background: white;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .thumbnail-item img {
        width: 100%;
        height: auto;
        display: block;
    }

    .delete-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        border: none;
        padding: 0.25rem 0.5rem;
        cursor: pointer;
        font-size: 0.75rem;
        transition: background 0.3s;
    }

    .empty-state {
        text-align: center;
        color: #6c757d;
        padding: 2rem;
    }

    .alert {
    }

    .alert-error {
        color: #ff142b;
    }

    .alert-success {
        color: #14a035;
    }

    #preview-canvas {
        border: 1px solid #ddd;
        max-width: 100%;
        display: block;
        margin-bottom: 1rem;
        cursor: grab;
        background: white;
        touch-action: none;
        -webkit-user-select: none;
        user-select: none;
    }

    .loading {
        display: none;
        text-align: center;
        padding: 1rem;
    }

    .loading.show {
        display: block;
    }

    /* Confirmation Modal */
    .confirm-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .confirm-modal.show {
        display: flex;
    }

    .confirm-modal-content {
        background: white;
        padding: 2rem;
        border: 2px solid #000;
        box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 90%;
    }

    .confirm-modal-message {
        margin-bottom: 1.5rem;
        font-size: 1rem;
        color: #333;
    }

    .confirm-modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .confirm-modal-buttons button {
        padding: 0.5rem 1.5rem;
        font-weight: bold;
        cursor: pointer;
    }

    .confirm-modal-buttons .btn-cancel {
        background: #e0e0e0;
    }

    .confirm-modal-buttons .btn-confirm {
        background: #ff4444;
        color: white;
    }

    .confirm-modal-buttons .btn-confirm:hover {
        background: #cc0000;
    }

    @media (max-width: 768px) {
        .edit-container {
            grid-template-columns: 1fr;
            height: auto;
            overflow: visible;
        }
        
        .main-section,
        .sidebar {
            overflow-y: visible;
            overflow-x: hidden;
        }
    }
</style>

<div class="edit-container">
    <div class="main-section">

        <div id="alert-container"></div>

        <!-- Webcam Area -->
        <div class="webcam-area" id="webcam-area">
            <div id="camera-permission-error" class="camera-permission-error" style="display: none;">
                <p>Unable to access camera. Please check your permissions or use the upload option instead.</p>
            </div>
            <video id="video-stream" autoplay playsinline muted style="display: none;"></video>
            <div class="webcam-controls" id="webcam-controls" style="display: none;">
                <button type="button" class="capture-photo-btn" id="capture-photo-btn">
                    📸 Capture Photo
                </button>
                <button type="button" class="toggle-upload-btn" id="toggle-to-upload-btn">
                    📁 Upload Image
                </button>
            </div>
            <p id="requesting-camera" style="text-align: center; color: #666;">Requesting camera access...</p>
        </div>

        <!-- Upload Area -->
        <div class="upload-area hidden" id="upload-area">
            <img id="preview-image" alt="Preview">
            <div id="upload-prompt">
                <p style="margin-bottom: 1rem;">Drag & drop an image here, or click to select</p>
                <button type="button" class="upload-btn" onclick="document.getElementById('image-upload').click()">
                    Choose Image
                </button>
                <button type="button" class="upload-btn" id="toggle-to-webcam-btn">
                    🎥 Use Webcam
                </button>
            </div>
            <input type="file" id="image-upload" class="upload-input" accept="image/jpeg,image/jpg,image/png,image/gif">
        </div>

        <!-- Canvas Preview -->
        <div id="canvas-container" style="display: none; margin-bottom: 2rem;">
            <canvas id="preview-canvas" style="border: 1px solid #ddd; max-width: 100%; display: block; margin-bottom: 1rem;"></canvas>
            <p style="color: #6c757d; font-size: 0.9rem;">Drag the overlay to position it</p>
        </div>

        <!-- Overlays Selection -->
        <div class="overlays-section">
            <div class="overlays-grid" id="overlays-grid">
                <?php if (empty($superposableImages)): ?>
                    <p class="empty-state">No overlays available. Please add overlay images to /public/assets/overlays/</p>
                <?php else: ?>
                    <?php foreach ($superposableImages as $overlay): ?>
                        <label class="overlay-item" data-overlay-id="<?= htmlspecialchars($overlay['id']) ?>">
                            <input type="radio" name="overlay" value="<?= htmlspecialchars($overlay['id']) ?>">
                            <div class="overlay-preview">
                                <img src="/assets/overlays/<?= htmlspecialchars($overlay['filename']) ?>" 
                                     alt="<?= htmlspecialchars($overlay['name']) ?>"
                                     style="max-width: 100%; max-height: 100%;"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<span>📄</span>'">
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Capture Button -->
        <button type="button" class="capture-btn" id="capture-btn" disabled>
            Create Image
        </button>

        <div class="loading" id="loading">
            <p>Composing your image, please wait...</p>
        </div>
    </div>

    <!-- Sidebar with User's Images -->
    <div class="sidebar">
        <div class="thumbnails-grid" id="thumbnails-grid">
            <div class="empty-state">Loading...</div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="confirm-modal" id="confirm-modal">
    <div class="confirm-modal-content">
        <div class="confirm-modal-message" id="confirm-modal-message"></div>
        <div class="confirm-modal-buttons">
            <button type="button" class="btn-cancel" id="confirm-cancel">Cancel</button>
            <button type="button" class="btn-confirm" id="confirm-ok">Delete</button>
        </div>
    </div>
</div>

<script>
    const uploadArea = document.getElementById('upload-area');
    const imageUpload = document.getElementById('image-upload');
    const previewImage = document.getElementById('preview-image');
    const uploadPrompt = document.getElementById('upload-prompt');
    const captureBtn = document.getElementById('capture-btn');
    const overlaysGrid = document.getElementById('overlays-grid');
    const thumbnailsGrid = document.getElementById('thumbnails-grid');
    const alertContainer = document.getElementById('alert-container');
    const loading = document.getElementById('loading');
    const canvasContainer = document.getElementById('canvas-container');
    const canvas = document.getElementById('preview-canvas');
    const ctx = canvas.getContext('2d');

    // Confirmation modal elements
    const confirmModal = document.getElementById('confirm-modal');
    const confirmMessage = document.getElementById('confirm-modal-message');
    const confirmOkBtn = document.getElementById('confirm-ok');
    const confirmCancelBtn = document.getElementById('confirm-cancel');

    // Webcam elements
    const webcamArea = document.getElementById('webcam-area');
    const videoStream = document.getElementById('video-stream');
    const webcamControls = document.getElementById('webcam-controls');
    const capturePhotoBtn = document.getElementById('capture-photo-btn');
    const toggleToUploadBtn = document.getElementById('toggle-to-upload-btn');
    const toggleToWebcamBtn = document.getElementById('toggle-to-webcam-btn');
    const camerPermissionError = document.getElementById('camera-permission-error');
    const requestingCamera = document.getElementById('requesting-camera');

    let selectedFile = null;
    let selectedOverlayId = null;
    let selectedOverlayImage = null;
    let baseImageData = null;
    let overlayX = 0;
    let overlayY = 0;
    let overlayWidth = 0;
    let overlayHeight = 0;
    let isDragging = false;
    let isResizing = false;
    let dragStartX = 0;
    let dragStartY = 0;
    const resizeHandleSize = 24;
    
    // Webcam state
    let mediaStream = null;
    let useWebcam = true;  // Start with webcam by default

    // Initialize webcam on page load
    async function initializeWebcam() {
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' },
                audio: false
            });
            
            videoStream.srcObject = mediaStream;
            videoStream.style.display = 'block';
            webcamControls.style.display = 'flex';
            requestingCamera.style.display = 'none';
            camerPermissionError.style.display = 'none';
            useWebcam = true;
        } catch (error) {
            // Camera access denied - show error message and fallback to upload
            camerPermissionError.style.display = 'block';
            requestingCamera.style.display = 'none';
            videoStream.style.display = 'none';
            webcamControls.style.display = 'none';
            // Show upload area as fallback
            setTimeout(() => {
                switchToUpload();
            }, 2000);
        }
    }

    // Capture photo from webcam
    function capturePhotoFromWebcam() {
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = videoStream.videoWidth;
        tempCanvas.height = videoStream.videoHeight;
        
        if (tempCanvas.width === 0 || tempCanvas.height === 0) {
            showAlert('Video not ready. Please wait a moment and try again.', 'error');
            return;
        }

        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(videoStream, 0, 0);

        // Stop video stream and hide webcam area
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
        webcamArea.classList.add('hidden');

        // Convert canvas to blob and create file
        tempCanvas.toBlob((blob) => {
            const file = new File([blob], 'webcam-photo.jpg', { type: 'image/jpeg' });
            handleFileSelect(file);
        }, 'image/jpeg', 0.95);
    }

    // Switch to upload mode
    function switchToUpload() {
        useWebcam = false;
        webcamArea.classList.add('hidden');
        uploadArea.classList.remove('hidden');
        
        // Clear inline styles to let CSS take over
        uploadArea.style.display = '';
        uploadPrompt.style.display = '';
        canvasContainer.style.display = 'none'; // Hide canvas if visible
        
        // Stop video stream
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
    }

    // Switch to webcam mode
    async function switchToWebcam() {
        useWebcam = true;
        uploadArea.classList.add('hidden');
        webcamArea.classList.remove('hidden');
        
        // Reset form when switching back
        selectedFile = null;
        baseImageData = null;
        cachedBaseImage = null;
        imageUpload.value = '';
        canvasContainer.style.display = 'none';
        
        // Initialize webcam again
        try {
            await initializeWebcam();
        } catch (error) {
            // Error already handled in initializeWebcam
        }
    }

    // Event listeners for webcam controls
    capturePhotoBtn.addEventListener('click', capturePhotoFromWebcam);
    toggleToUploadBtn.addEventListener('click', switchToUpload);
    toggleToWebcamBtn.addEventListener('click', switchToWebcam);

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    // File input change
    imageUpload.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showAlert('Please select a valid image file (JPG, PNG, or GIF)', 'error');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('File size must be less than 5MB', 'error');
            return;
        }

        selectedFile = file;

        // Load image and show canvas
        const reader = new FileReader();
        reader.onload = async (e) => {
            const img = new Image();
            img.onload = async () => {
                // Validate image dimensions
                const imgPixels = img.width * img.height;
                if (img.width < 200 || img.height < 200) {
                    showAlert(`Image too small (${img.width}x${img.height}). Minimum 200x200 pixels. Please upload a larger image.`, 'error');
                    imageUpload.value = '';
                    return;
                }
                if (img.width > 3000 || img.height > 3000 || imgPixels > 5000000) {
                    showAlert(`Image too large (${img.width}x${img.height}). Maximum 3000x3000 pixels or 5 megapixels. Please upload a smaller image.`, 'error');
                    imageUpload.value = '';
                    return;
                }

                baseImageData = e.target.result;
                uploadArea.style.display = 'none'; // Hide entire upload area
                uploadPrompt.style.display = 'none';

                // Setup canvas and show it with base image
                setupCanvas();
                canvasContainer.style.display = 'block';

                // If overlay is already selected, load it
                if (selectedOverlayId) {
                    await loadOverlayImage();
                }

                updateCaptureButton();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Load overlay image from the server
    async function loadOverlayImage() {
        if (!selectedOverlayId) return;

        return new Promise((resolve) => {
            const overlayItem = document.querySelector(`[data-overlay-id="${selectedOverlayId}"]`);
            const overlayImg = overlayItem.querySelector('img');
            const img = new Image();

            img.onload = () => {
                selectedOverlayImage = img;
                
                // Get base image dimensions
                const baseWidth = canvas.width;
                const baseHeight = canvas.height;
                
                // Start with overlay's natural dimensions
                let newWidth = img.width;
                let newHeight = img.height;
                
                // Check if overlay is larger than base image and scale down if needed
                if (newWidth > baseWidth || newHeight > baseHeight) {
                    // Calculate scale factor to fit within base image (80% for some padding)
                    const maxWidth = baseWidth * 0.8;
                    const maxHeight = baseHeight * 0.8;
                    const scaleX = maxWidth / newWidth;
                    const scaleY = maxHeight / newHeight;
                    const scale = Math.min(scaleX, scaleY);
                    
                    newWidth = Math.floor(newWidth * scale);
                    newHeight = Math.floor(newHeight * scale);
                }
                
                overlayWidth = newWidth;
                overlayHeight = newHeight;
                
                // Center the overlay on the base image
                overlayX = Math.floor((baseWidth - overlayWidth) / 2);
                overlayY = Math.floor((baseHeight - overlayHeight) / 2);
                
                // Redraw canvas with the overlay
                redrawCanvas();
                
                resolve();
            };

            img.onerror = () => {
                showAlert('Failed to load overlay image', 'error');
                resolve();
            };

            img.src = overlayImg.src;
        });
    }

    // Setup canvas dimensions
    function setupCanvas() {
        if (!baseImageData) return;

        const img = new Image();
        img.onload = () => {
            canvas.width = img.width;
            canvas.height = img.height;
            redrawCanvas(); // Draw after dimensions are set
        };
        img.src = baseImageData;
    }

    // Cached images for faster rendering
    let cachedBaseImage = null;
    let pendingRedraw = false;

    // Redraw canvas with base image and overlay
    function redrawCanvas() {
        if (!baseImageData || canvas.width === 0) return;

        if (pendingRedraw) return; // Skip if already scheduled
        pendingRedraw = true;

        requestAnimationFrame(() => {
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw base image (load only once)
            if (!cachedBaseImage) {
                cachedBaseImage = new Image();
                cachedBaseImage.src = baseImageData;
                cachedBaseImage.onload = () => {
                    ctx.drawImage(cachedBaseImage, 0, 0, canvas.width, canvas.height);
                    // Draw overlay if available
                    if (selectedOverlayImage) {
                        ctx.globalAlpha = 0.9;
                        ctx.drawImage(
                            selectedOverlayImage,
                            overlayX,
                            overlayY,
                            overlayWidth,
                            overlayHeight
                        );
                        ctx.globalAlpha = 1.0;

                        // Draw border around overlay
                        ctx.strokeStyle = '#007bff';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(
                            overlayX,
                            overlayY,
                            overlayWidth,
                            overlayHeight
                        );

                        // Draw resize handle at bottom-right corner
                        ctx.fillStyle = '#007bff';
                        ctx.fillRect(
                            overlayX + overlayWidth - resizeHandleSize,
                            overlayY + overlayHeight - resizeHandleSize,
                            resizeHandleSize,
                            resizeHandleSize
                        );
                        ctx.strokeStyle = '#ffffff';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(
                            overlayX + overlayWidth - resizeHandleSize,
                            overlayY + overlayHeight - resizeHandleSize,
                            resizeHandleSize,
                            resizeHandleSize
                        );
                    }
                    pendingRedraw = false;
                };
            } else {
                ctx.drawImage(cachedBaseImage, 0, 0, canvas.width, canvas.height);
                // Draw overlay if available
                if (selectedOverlayImage) {
                    ctx.globalAlpha = 0.9;
                    ctx.drawImage(
                        selectedOverlayImage,
                        overlayX,
                        overlayY,
                        overlayWidth,
                        overlayHeight
                    );
                    ctx.globalAlpha = 1.0;

                    // Draw border around overlay
                    ctx.strokeStyle = '#007bff';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(
                        overlayX,
                        overlayY,
                        overlayWidth,
                        overlayHeight
                    );

                    // Draw resize handle at bottom-right corner
                    ctx.fillStyle = '#007bff';
                    ctx.fillRect(
                        overlayX + overlayWidth - resizeHandleSize,
                        overlayY + overlayHeight - resizeHandleSize,
                        resizeHandleSize,
                        resizeHandleSize
                    );
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(
                        overlayX + overlayWidth - resizeHandleSize,
                        overlayY + overlayHeight - resizeHandleSize,
                        resizeHandleSize,
                        resizeHandleSize
                    );
                }
                pendingRedraw = false;
            }
        });
    }

    // Helper function to get canvas coordinates from mouse or touch event
    function getCanvasCoords(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        let clientX, clientY;
        
        if (e.touches) {
            // Touch event
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            // Mouse event
            clientX = e.clientX;
            clientY = e.clientY;
        }
        
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    // Canvas mouse and touch events for dragging overlay
    canvas.addEventListener('mousedown', handleCanvasDown);
    canvas.addEventListener('touchstart', handleCanvasDown);

    function handleCanvasDown(e) {
        if (!selectedOverlayImage) return;

        const coords = getCanvasCoords(e);
        const x = coords.x;
        const y = coords.y;

        // Check if clicking on resize handle
        const handleX = overlayX + overlayWidth - resizeHandleSize;
        const handleY = overlayY + overlayHeight - resizeHandleSize;
        
        if (
            x >= handleX &&
            x <= handleX + resizeHandleSize &&
            y >= handleY &&
            y <= handleY + resizeHandleSize
        ) {
            isResizing = true;
            dragStartX = x;
            dragStartY = y;
            canvas.style.cursor = 'nwse-resize';
            e.preventDefault();
            return;
        }

        // Check if click is within overlay bounds for dragging
        if (
            x >= overlayX &&
            x <= overlayX + overlayWidth &&
            y >= overlayY &&
            y <= overlayY + overlayHeight
        ) {
            isDragging = true;
            dragStartX = x - overlayX;
            dragStartY = y - overlayY;
            canvas.style.cursor = 'grabbing';
            e.preventDefault();
        }
    }

    canvas.addEventListener('mousemove', handleCanvasMove);
    canvas.addEventListener('touchmove', handleCanvasMove);

    function handleCanvasMove(e) {
        const coords = getCanvasCoords(e);
        const x = coords.x;
        const y = coords.y;

        if (isResizing) {
            // Handle resizing
            const deltaX = x - dragStartX;
            const deltaY = y - dragStartY;
            
            // Maintain aspect ratio
            const aspectRatio = selectedOverlayImage.width / selectedOverlayImage.height;
            let newWidth = overlayWidth + deltaX;
            let newHeight = newWidth / aspectRatio;
            
            // Minimum size constraint
            const minSize = 50;
            if (newWidth < minSize) {
                newWidth = minSize;
                newHeight = minSize / aspectRatio;
            }
            
            // Maximum size constraint (don't exceed canvas)
            if (overlayX + newWidth > canvas.width) {
                newWidth = canvas.width - overlayX;
                newHeight = newWidth / aspectRatio;
            }
            if (overlayY + newHeight > canvas.height) {
                newHeight = canvas.height - overlayY;
                newWidth = newHeight * aspectRatio;
            }
            
            overlayWidth = newWidth;
            overlayHeight = newHeight;
            dragStartX = x;
            dragStartY = y;
            
            redrawCanvas();
            e.preventDefault();
            return;
        }

        if (isDragging) {
            // Handle dragging
            overlayX = Math.max(0, Math.min(x - dragStartX, canvas.width - overlayWidth));
            overlayY = Math.max(0, Math.min(y - dragStartY, canvas.height - overlayHeight));
            redrawCanvas();
            e.preventDefault();
            return;
        }
        // Update cursor when hovering
        if (selectedOverlayImage) {
            // Check resize handle
            const handleX = overlayX + overlayWidth - resizeHandleSize;
            const handleY = overlayY + overlayHeight - resizeHandleSize;
            
            if (
                x >= handleX &&
                x <= handleX + resizeHandleSize &&
                y >= handleY &&
                y <= handleY + resizeHandleSize
            ) {
                canvas.style.cursor = 'nwse-resize';
            } else if (
                x >= overlayX &&
                x <= overlayX + overlayWidth &&
                y >= overlayY &&
                y <= overlayY + overlayHeight
            ) {
                canvas.style.cursor = 'grab';
            } else {
                canvas.style.cursor = 'default';
            }
        }
    }

    document.addEventListener('mouseup', handleCanvasUp);
    document.addEventListener('touchend', handleCanvasUp);

    function handleCanvasUp(e) {
        if (isDragging || isResizing) {
            isDragging = false;
            isResizing = false;
            if (selectedOverlayImage) {
                canvas.style.cursor = 'grab';
            } else {
                canvas.style.cursor = 'default';
            }
            e.preventDefault();
        }
    }

    canvas.addEventListener('mouseleave', (e) => {
        if (!isDragging && selectedOverlayImage) {
            canvas.style.cursor = 'default';
        }
    });

    // Show cursor feedback
    canvas.addEventListener('mouseover', () => {
        if (selectedOverlayImage) {
            canvas.style.cursor = 'grab';
        }
    });

    // Overlay selection
    overlaysGrid.addEventListener('click', async (e) => {
        const overlayItem = e.target.closest('.overlay-item');
        if (!overlayItem) return;

        // Remove previous selection
        document.querySelectorAll('.overlay-item').forEach(item => {
            item.classList.remove('selected');
        });

        // Select this overlay
        overlayItem.classList.add('selected');
        const radio = overlayItem.querySelector('input[type="radio"]');
        radio.checked = true;
        selectedOverlayId = radio.value;

        // Load overlay image if base image is selected
        if (selectedFile) {
            await loadOverlayImage();
            setupCanvas();
            previewImage.style.display = 'none'; // Hide the simple preview
            canvasContainer.style.display = 'block'; // Show the interactive canvas
        }

        updateCaptureButton();
    });

    // Update capture button state
    function updateCaptureButton() {
        captureBtn.disabled = !(selectedFile && selectedOverlayId);
    }

    // Capture/Create image
    captureBtn.addEventListener('click', async () => {
        if (!selectedFile || !selectedOverlayId) {
            showAlert('Please select both an image and an overlay', 'error');
            return;
        }

        // Show loading
        loading.classList.add('show');
        captureBtn.disabled = true;

        try {
            // Get fresh CSRF token
            const tokenResponse = await fetch('/image/getCsrfToken');
            if (!tokenResponse.ok) {
                throw new Error('Failed to get CSRF token');
            }
            const tokenData = await tokenResponse.json();
            const csrfToken = tokenData.csrf_token;

            // Prepare form data
            const formData = new FormData();
            formData.append('base_image', selectedFile);
            formData.append('overlay_id', selectedOverlayId);
            formData.append('overlay_x', Math.round(overlayX));
            formData.append('overlay_y', Math.round(overlayY));
            formData.append('overlay_width', Math.round(overlayWidth));
            formData.append('overlay_height', Math.round(overlayHeight));
            formData.append('csrf_token', csrfToken);

            const response = await fetch('/image/compose', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const text = await response.text();
            
            try {
                const data = JSON.parse(text);

                if (data.success) {
                    showAlert('Image created successfully!', 'success');
                    await resetForm();
                    loadUserImages(); // Reload thumbnails
                } else {
                    showAlert(data.error || 'Failed to create image', 'error');
                }
            } catch (parseError) {
                showAlert('Server error: Invalid response from server', 'error');
            }
        } finally {
            loading.classList.remove('show');
            updateCaptureButton();
        }
    });

    // Reset form
    async function resetForm() {
        selectedFile = null;
        selectedOverlayId = null;
        selectedOverlayImage = null;
        baseImageData = null;
        cachedBaseImage = null;
        overlayX = 0;
        overlayY = 0;
        overlayWidth = 0;
        overlayHeight = 0;
        isDragging = false;
        isResizing = false;
        imageUpload.value = '';
        canvasContainer.style.display = 'none'; // Hide canvas
        
        // Clear any inline styles that might be blocking visibility
        uploadArea.style.display = '';
        uploadPrompt.style.display = '';
        webcamArea.style.display = '';
        
        // Show appropriate area
        if (useWebcam) {
            uploadArea.classList.add('hidden');
            webcamArea.classList.remove('hidden');
            // Reinitialize webcam if stopped
            if (!mediaStream) {
                try {
                    await initializeWebcam();
                } catch (error) {
                    // Error already handled in initializeWebcam
                }
            }
        } else {
            uploadArea.classList.remove('hidden');
            uploadPrompt.style.display = 'block'; // Make sure upload prompt is visible
            webcamArea.classList.add('hidden');
        }
        
        document.querySelectorAll('.overlay-item').forEach(item => {
            item.classList.remove('selected');
        });
        document.querySelectorAll('input[name="overlay"]').forEach(radio => {
            radio.checked = false;
        });
    }

    // Load user's images
    async function loadUserImages() {
        try {
            const response = await fetch('/image/getUserImages');
            const data = await response.json();

            thumbnailsGrid.innerHTML = '';
            
            if (data.success && data.images.length > 0) {
                data.images.forEach(image => {
                    const item = document.createElement('div');
                    item.className = 'thumbnail-item';
                    item.dataset.imageId = image.id;
                    
                    const img = document.createElement('img');
                    img.src = image.url;
                    img.alt = 'User image';
                    
                    const btn = document.createElement('button');
                    btn.className = 'delete-btn';
                    btn.textContent = 'Delete';
                    btn.type = 'button';
                    btn.addEventListener('click', () => deleteImage(image.id));
                    
                    item.appendChild(img);
                    item.appendChild(btn);
                    thumbnailsGrid.appendChild(item);
                });
            } else {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'empty-state';
                emptyDiv.textContent = data.success ? 'No images yet. Create your first one!' : 'Failed to load images';
                thumbnailsGrid.appendChild(emptyDiv);
            }
        } catch (error) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-state';
            emptyDiv.textContent = 'Failed to load images';
            thumbnailsGrid.innerHTML = '';
            thumbnailsGrid.appendChild(emptyDiv);
        }
    }

    // Delete image
    async function deleteImage(imageId) {
        const confirmed = await showConfirm('Are you sure you want to delete this image?');
        if (!confirmed) {
            return;
        }

        try {
            // Get fresh CSRF token
            const tokenResponse = await fetch('/image/getCsrfToken');
            if (!tokenResponse.ok) {
                throw new Error('Failed to get CSRF token');
            }
            const tokenData = await tokenResponse.json();

            const formData = new FormData();
            formData.append('image_id', imageId);
            formData.append('csrf_token', tokenData.csrf_token);

            const response = await fetch('/image/delete', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Image deleted successfully', 'success');
                loadUserImages(); // Reload thumbnails
            } else {
                showAlert(data.error || 'Failed to delete image', 'error');
            }
        } catch (error) {
            showAlert('An error occurred while deleting image', 'error');
        }
    }

    // Show alert message
    function showAlert(message, type) {
        const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
        alertContainer.innerHTML = '';
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert ' + alertClass;
        alertDiv.textContent = message;
        
        alertContainer.appendChild(alertDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    // Show confirmation modal
    function showConfirm(message) {
        return new Promise((resolve) => {
            confirmMessage.textContent = message;
            confirmModal.classList.add('show');

            const handleOk = () => {
                cleanup();
                resolve(true);
            };

            const handleCancel = () => {
                cleanup();
                resolve(false);
            };

            const cleanup = () => {
                confirmModal.classList.remove('show');
                confirmOkBtn.removeEventListener('click', handleOk);
                confirmCancelBtn.removeEventListener('click', handleCancel);
            };

            confirmOkBtn.addEventListener('click', handleOk);
            confirmCancelBtn.addEventListener('click', handleCancel);
        });
    }

    // Load user images on page load
    loadUserImages();
    
    // Initialize webcam on page load
    initializeWebcam().catch(() => {
        // Error already handled in initializeWebcam
    });
</script>
