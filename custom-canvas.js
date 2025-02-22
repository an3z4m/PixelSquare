const canvas = document.getElementById('grid-canvas');
const ctx = canvas.getContext('2d');

let scale = 1; // Initial scale factor
let offsetX = 0; // Horizontal pan offset
let offsetY = 0; // Vertical pan offset
let isDragging = false; // Track whether the user is dragging
let startX, startY; // Track the starting position of a drag
const zoomFactor = 1.05; // Zoom sensitivity (greater than 1 for smooth scaling)
const minScale = 1;
const maxScale = 20; // Max limit for zooming
let needsRedraw = false;

// Define missing variables
const backgroundImage = new Image();
// const backgroundSrc = 'your-image-path.png'; // Set the correct image source
// const squareSize = 50; // Define grid square size
// let isEditingMode = false; // Default editing mode

backgroundImage.src = backgroundSrc;
backgroundImage.onload = () => {
    drawBackgroundAndGrid();
};

// Function to draw the background image and grid
function drawBackgroundAndGrid() {
    // Clamp scale values
    scale = Math.max(minScale, Math.min(maxScale, scale));

    // Clear canvas and apply transformations
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();

    // Constrain panning within valid bounds
    offsetX = Math.min(0, Math.max(offsetX, canvas.width - (canvas.width * scale)));
    offsetY = Math.min(0, Math.max(offsetY, canvas.height - (canvas.height * scale)));

    ctx.translate(offsetX, offsetY);
    ctx.scale(scale, scale);

    // Draw the background
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

    // Draw the grid
    drawGrid();

    ctx.restore();
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

// Dummy function for reserved areas
function drawReservedAreas() {
    // Define logic to highlight reserved grid areas if necessary
}

// Handle zooming with the mouse wheel
canvas.addEventListener('wheel', function(event) {
    event.preventDefault();

    // Get mouse position relative to the canvas
    const rect = canvas.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    // Calculate the zoom factor
    const delta = event.deltaY < 0 ? zoomFactor : 1 / zoomFactor;

    // Calculate the new scale and offset
    const newScale = scale * delta;
    if (newScale >= minScale && newScale <= maxScale) {
        offsetX = mouseX - (mouseX - offsetX) * (newScale / scale);
        offsetY = mouseY - (mouseY - offsetY) * (newScale / scale);
        scale = newScale;
        requestRedraw();
    }
}, { passive: false });

// Handle panning (dragging)
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
        requestRedraw();
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

// Handle touch gestures
let touchStartDistance = null;
let touchStartScale = scale;
let touchStartOffsetX = offsetX;
let touchStartOffsetY = offsetY;

canvas.addEventListener('touchstart', function(event) {
    if (event.touches.length === 1) {
        isDragging = true;
        startX = event.touches[0].clientX - offsetX;
        startY = event.touches[0].clientY - offsetY;
    } else if (event.touches.length === 2) {
        const [touch1, touch2] = event.touches;
        touchStartDistance = Math.hypot(
            touch2.clientX - touch1.clientX,
            touch2.clientY - touch1.clientY
        );
        touchStartScale = scale;
        touchStartOffsetX = offsetX;
        touchStartOffsetY = offsetY;
    }
});

// canvas.addEventListener('touchmove', function(event) {
//     event.preventDefault();
    
//     if (event.touches.length === 1 && isDragging) {
//         // Panning (Dragging)
//         offsetX = event.touches[0].clientX - startX;
//         offsetY = event.touches[0].clientY - startY;
//         requestRedraw();
//     } else if (event.touches.length === 2) {
//         // Pinch Zoom
//         const [touch1, touch2] = event.touches;
//         const touchDistance = Math.hypot(
//             touch2.clientX - touch1.clientX,
//             touch2.clientY - touch1.clientY
//         );

//         if (touchStartDistance !== null) {
//             let zoomDelta = touchDistance / touchStartDistance;
//             let newScale = touchStartScale * zoomDelta;

//             if (newScale >= minScale && newScale <= maxScale) {
//                 // Get pinch midpoint (center of two fingers)
//                 const centerX = (touch1.clientX + touch2.clientX) / 2;
//                 const centerY = (touch1.clientY + touch2.clientY) / 2;

//                 // Adjust offsets to keep the zoom focused on pinch midpoint
//                 offsetX = centerX - (centerX - touchStartOffsetX) * (newScale / touchStartScale);
//                 offsetY = centerY - (centerY - touchStartOffsetY) * (newScale / touchStartScale);

//                 scale = newScale;
//                 requestRedraw();
//             }
//         }
//     }
// });

canvas.addEventListener('touchmove', function(event) {
    event.preventDefault();

    if (needsRedraw) return; 
    if (event.touches.length === 1 && isDragging) {
        // Panning (Dragging)
        offsetX = event.touches[0].clientX - startX;
        offsetY = event.touches[0].clientY - startY;
        requestRedraw();
    } else if (event.touches.length === 2) {
        // Fixed Zoom Step
        const [touch1, touch2] = event.touches;
        const centerX = (touch1.clientX + touch2.clientX) / 2;
        const centerY = (touch1.clientY + touch2.clientY) / 2;

        let zoomFactor = 1.05; // Default zoom-in factor

        if (touchStartDistance !== null) {
            const touchDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );

            if (touchDistance < touchStartDistance) {
                zoomFactor = 0.95; // Zoom out when fingers get closer
            }
        }

        let newScale = scale * zoomFactor;
        if (newScale >= minScale && newScale <= maxScale) {
            // Adjust offsets to keep zoom centered
            offsetX = centerX - (centerX - offsetX) * zoomFactor;
            offsetY = centerY - (centerY - offsetY) * zoomFactor;
            scale = newScale;
            requestRedraw();
        }

        // Store current distance for next move
        touchStartDistance = Math.hypot(
            touch2.clientX - touch1.clientX,
            touch2.clientY - touch1.clientY
        );
    }
});


canvas.addEventListener('touchend', function(event) {
    if (event.touches.length === 0) {
        isDragging = false;
        touchStartDistance = null;
    }
});

// Prevent page scrolling when interacting with the canvas
document.body.addEventListener('touchmove', function(event) {
    if (event.target === canvas) {
        event.preventDefault();
    }
}, { passive: false });

// Prevent right-click menu
canvas.addEventListener('contextmenu', function(event) {
    event.preventDefault();
});

// Optimized redraw function
function requestRedraw() {
    if (!needsRedraw) {
        needsRedraw = true;
        drawBackgroundAndGrid();
        needsRedraw = false;

        // requestAnimationFrame(() => {
        //     drawBackgroundAndGrid();
        //     needsRedraw = false;
        // });
    }
}

