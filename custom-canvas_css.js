const canvas = document.getElementById('grid-canvas');
const backgroundImage = new Image();


backgroundImage.onload = () => {
    drawBackgroundAndGrid(); // Draw the background and grid once the image is loaded
};

// Function to draw the background image and grid
function drawBackgroundAndGrid() {
    // Clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Save the current canvas state
    ctx.save();

    // prevent zoom-out out the canvas area
    scale = Math.max(scale, 1); 

    // add boundary checks to prevent the user from panning outside the canvas area
    offsetX = Math.min(Math.max(offsetX, -canvas.width * (scale - 1)), 0);
    offsetY = Math.min(Math.max(offsetY, -canvas.height * (scale - 1)), 0);
    
    // Apply the current scale and offset transformations
    ctx.translate(offsetX, offsetY);
    ctx.scale(scale, scale);

    // Draw the background image
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

    // Draw the grid (or any other content)
    drawGrid();

    // Restore the canvas state
    ctx.restore();
}

// // Load the background image
backgroundImage.src = backgroundSrc;

const ctx = canvas.getContext('2d');
let scale = 1;
let offsetX = 0;
let offsetY = 0;
let isDragging = false;
let startX, startY;
const zoomFactor = 1.05;
const minScale = 1;
const maxScale = 10;

// Apply initial transform
canvas.style.transformOrigin = "0 0"; // Ensure zooming happens from the top-left corner
updateTransform();

// Handle zoom with mouse wheel
canvas.addEventListener('wheel', function(event) {
    event.preventDefault();
    const delta = event.deltaY < 0 ? zoomFactor : 1 / zoomFactor;
    const newScale = scale * delta;
    if (newScale >= minScale && newScale <= maxScale) {
        scale = newScale;
        updateTransform();
    }
}, { passive: false });

// Handle panning with mouse
canvas.addEventListener('mousedown', function(event) {
    isDragging = true;
    startX = event.clientX - offsetX;
    startY = event.clientY - offsetY;
    canvas.style.cursor = 'grabbing';
});

canvas.addEventListener('mousemove', function(event) {
    if (isDragging) {
        offsetX = event.clientX - startX;
        offsetY = event.clientY - startY;
        updateTransform();
    }
});

canvas.addEventListener('mouseup', function() {
    isDragging = false;
    canvas.style.cursor = 'grab';
});

canvas.addEventListener('mouseleave', function() {
    isDragging = false;
    canvas.style.cursor = 'grab';
});

// Handle touch gestures using Pointer Events (native & smooth)
canvas.addEventListener('pointerdown', function(event) {
    if (event.pointerType === 'touch') {
        isDragging = true;
        startX = event.clientX - offsetX;
        startY = event.clientY - offsetY;
    }
});

canvas.addEventListener('pointermove', function(event) {
    if (event.pointerType === 'touch' && isDragging) {
        offsetX = event.clientX - startX;
        offsetY = event.clientY - startY;
        updateTransform();
    }
});

canvas.addEventListener('pointerup', function() {
    isDragging = false;
});

// Smooth native zoom using pinch gesture
canvas.addEventListener('gesturestart', function(event) {
    event.preventDefault();
});

canvas.addEventListener('gesturechange', function(event) {
    event.preventDefault();
    let newScale = scale * event.scale;
    if (newScale >= minScale && newScale <= maxScale) {
        scale = newScale;
        updateTransform();
    }
});

// Prevent page scrolling on touch move
document.body.addEventListener('touchmove', function(event) {
    if (event.target === canvas) {
        event.preventDefault();
    }
}, { passive: false });

// Function to apply CSS transforms (SUPER FAST)
function updateTransform() {
    canvas.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
}


// Function to draw the grid
function drawGrid() {
    ctx.strokeStyle = isEditingMode ? 'rgba(0, 0, 255, 0.5)' : 'rgba(255, 0, 0, 0.2)';
    ctx.lineWidth = isEditingMode ? 0.5 : 0.2;

    for (let x = 0; x <= canvas.width; x += squareSize) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvas.height);
        ctx.stroke();
    }
    for (let y = 0; y <= canvas.height; y += squareSize) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }

    drawReservedAreas();
}