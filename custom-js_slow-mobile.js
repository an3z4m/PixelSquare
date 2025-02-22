const canvas = document.getElementById('grid-canvas');
const ctx = canvas.getContext('2d');
let scale = 1; // Initial scale factor
let offsetX = 0; // Horizontal pan offset
let offsetY = 0; // Vertical pan offset
let isDragging = false; // Track whether the user is dragging
let startX, startY; // Track the starting position of a drag
const backgroundImage = new Image();

// // Load the background image
backgroundImage.src = backgroundSrc;


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

// Handle zooming with the mouse wheel
canvas.addEventListener('wheel', function(event) {
    event.preventDefault();

    // Get the mouse position relative to the canvas
    const rect = canvas.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    // Calculate the zoom factor
    const zoomFactor = 1.1;
    const delta = event.deltaY < 0 ? zoomFactor : 1 / zoomFactor;

    // Calculate the new scale and offset
    const newScale = scale * delta;
    offsetX = mouseX - (mouseX - offsetX) * (newScale / scale);
    offsetY = mouseY - (mouseY - offsetY) * (newScale / scale);
    scale = newScale;

    // Redraw the canvas with the new scale and offset
    drawBackgroundAndGrid();
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

        // Redraw the canvas with the new offset
        drawBackgroundAndGrid();
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

// // Handle touch events for mobile devices
// canvas.addEventListener('touchstart', function(event) {
//     if (event.touches.length === 1) {
//         isDragging = true;
//         startX = event.touches[0].clientX - offsetX;
//         startY = event.touches[0].clientY - offsetY;
//     }
// });

// canvas.addEventListener('touchmove', function(event) {
//     if (isDragging && event.touches.length === 1) {
//         offsetX = event.touches[0].clientX - startX;
//         offsetY = event.touches[0].clientY - startY;

//         // Redraw the canvas with the new offset
//         drawBackgroundAndGrid();
//     }
// });

// canvas.addEventListener('touchend', function() {
//     isDragging = false;
// });

// // Prevent the page from scrolling when interacting with the canvas
// document.body.addEventListener('touchmove', function(event) {
//     if (event.target === canvas) {
//         event.preventDefault();
//     }
// }, { passive: false });


// let lastDistance = null; // Store the last pinch distance
// let lastTouchX = 0; // Store the initial touch point for panning
// let lastTouchY = 0; 

// // Handle pinch zoom (two-finger touch)
// canvas.addEventListener('touchmove', function(event) {
//     if (event.touches.length === 2) {
//         // Calculate the distance between the two touch points
//         const touch1 = event.touches[0];
//         const touch2 = event.touches[1];
//         const dx = touch2.clientX - touch1.clientX;
//         const dy = touch2.clientY - touch1.clientY;
//         const distance = Math.sqrt(dx * dx + dy * dy);

//         if (lastDistance !== null) {
//             const zoomFactor = distance / lastDistance;

//             // Adjust the scale and offset
//             const newScale = scale * zoomFactor;
//             if (newScale >= 0.1 && newScale <= 3) { // Limit zoom scale between 0.1 and 3
//                 scale = newScale;

//                 // Calculate the offset change based on pinch position
//                 const mouseX = (touch1.clientX + touch2.clientX) / 2 - canvas.offsetLeft;
//                 const mouseY = (touch1.clientY + touch2.clientY) / 2 - canvas.offsetTop;

//                 offsetX = mouseX - (mouseX - offsetX) * (scale / newScale);
//                 offsetY = mouseY - (mouseY - offsetY) * (scale / newScale);
                
//                 drawBackgroundAndGrid(); // Redraw the canvas with the updated zoom
//             }
//         }

//         // Store the current distance for the next move
//         lastDistance = distance;
//     }
// }, { passive: false });

// // Reset the distance when the touch ends
// canvas.addEventListener('touchend', function(event) {
//     if (event.touches.length < 2) {
//         lastDistance = null; // Reset pinch distance
//     }
// });

// // Handle panning with one finger
// canvas.addEventListener('touchstart', function(event) {
//     if (event.touches.length === 1) {
//         lastTouchX = event.touches[0].clientX;
//         lastTouchY = event.touches[0].clientY;
//     }
// });

// canvas.addEventListener('touchmove', function(event) {
//     if (event.touches.length === 1) {
//         const touch = event.touches[0];
//         const deltaX = touch.clientX - lastTouchX;
//         const deltaY = touch.clientY - lastTouchY;

//         // Update the offset based on finger movement
//         offsetX += deltaX;
//         offsetY += deltaY;

//         // Redraw the canvas with the new offset
//         drawBackgroundAndGrid();

//         // Update the last touch position
//         lastTouchX = touch.clientX;
//         lastTouchY = touch.clientY;
//     }
// });



// Handle touch events for mobile devices
// let touchStartDistance = null;
// let touchStartScale = scale;
// let touchStartOffsetX = offsetX;
// let touchStartOffsetY = offsetY;

// canvas.addEventListener('touchstart', function(event) {
//     if (event.touches.length === 1) {
//         // Single touch: start dragging
//         isDragging = true;
//         startX = event.touches[0].clientX - offsetX;
//         startY = event.touches[0].clientY - offsetY;
//     } else if (event.touches.length === 2) {
//         // Two touches: start pinch-to-zoom
//         const touch1 = event.touches[0];
//         const touch2 = event.touches[1];
//         touchStartDistance = Math.hypot(
//             touch2.clientX - touch1.clientX,
//             touch2.clientY - touch1.clientY
//         );
//         touchStartScale = scale;
//         touchStartOffsetX = offsetX;
//         touchStartOffsetY = offsetY;
//     }
// });

// canvas.addEventListener('touchmove', function(event) {
//     if (isDragging && event.touches.length === 1) {
//         // Single touch: continue dragging
//         offsetX = event.touches[0].clientX - startX;
//         offsetY = event.touches[0].clientY - startY;

//         // Redraw the canvas with the new offset
//         drawBackgroundAndGrid();
//     } else if (event.touches.length === 2) {
//         // Two touches: handle pinch-to-zoom
//         const touch1 = event.touches[0];
//         const touch2 = event.touches[1];
//         const touchDistance = Math.hypot(
//             touch2.clientX - touch1.clientX,
//             touch2.clientY - touch1.clientY
//         );

//         if (touchStartDistance !== null) {
//             // Calculate the new scale
//             const newScale = (touchDistance / touchStartDistance) * touchStartScale;
//             scale = newScale;

//             // Calculate the new offset to keep the zoom centered
//             const centerX = (touch1.clientX + touch2.clientX) / 2;
//             const centerY = (touch1.clientY + touch2.clientY) / 2;
//             offsetX = centerX - (centerX - touchStartOffsetX) * (scale / touchStartScale);
//             offsetY = centerY - (centerY - touchStartOffsetY) * (scale / touchStartScale);

//             // Redraw the canvas with the new scale and offset
//             drawBackgroundAndGrid();
//         }
//     }
// });

// canvas.addEventListener('touchend', function() {
//     isDragging = false;
//     touchStartDistance = null;
// });

// // Prevent the page from scrolling when interacting with the canvas
// document.body.addEventListener('touchmove', function(event) {
//     if (event.target === canvas) {
//         event.preventDefault();
//     }
// }, { passive: false });



// Handle touch events for mobile devices
let touchStartDistance = null;
let touchStartScale = scale;
let touchStartOffsetX = offsetX;
let touchStartOffsetY = offsetY;

let needsRedraw = false;

canvas.addEventListener('touchstart', function(event) {
    if (event.touches.length === 1) {
        // Single touch: start dragging
        isDragging = true;
        startX = event.touches[0].clientX - offsetX;
        startY = event.touches[0].clientY - offsetY;
    } else if (event.touches.length === 2) {
        // Two touches: start pinch-to-zoom
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

canvas.addEventListener('touchmove', function(event) {
    if (event.touches.length === 1 && isDragging) {
        // Single touch: continue dragging
        offsetX = event.touches[0].clientX - startX;
        offsetY = event.touches[0].clientY - startY;
        requestRedraw();
    } else if (event.touches.length === 2) {
        // Two touches: handle pinch-to-zoom
        const [touch1, touch2] = event.touches;
        const touchDistance = Math.hypot(
            touch2.clientX - touch1.clientX,
            touch2.clientY - touch1.clientY
        );

        if (touchStartDistance !== null) {
            const newScale = (touchDistance / touchStartDistance) * touchStartScale;
            scale = newScale;

            // Keep zoom centered
            const centerX = (touch1.clientX + touch2.clientX) / 2;
            const centerY = (touch1.clientY + touch2.clientY) / 2;
            offsetX = centerX - (centerX - touchStartOffsetX) * (scale / touchStartScale);
            offsetY = centerY - (centerY - touchStartOffsetY) * (scale / touchStartScale);
            requestRedraw();
        }
    }
});

canvas.addEventListener('touchend', function() {
    isDragging = false;
    touchStartDistance = null;
});

// Prevent page scrolling when interacting with the canvas
document.body.addEventListener('touchmove', function(event) {
    if (event.target === canvas) {
        event.preventDefault();
    }
}, { passive: false });

// Use requestAnimationFrame to optimize rendering
function requestRedraw() {
    if (!needsRedraw) {
        needsRedraw = true;
        requestAnimationFrame(() => {
            drawBackgroundAndGrid();
            needsRedraw = false;
        });
    }
}