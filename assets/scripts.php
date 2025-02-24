<script>
const canvas = document.getElementById('grid-canvas');
const ctx = canvas.getContext('2d');

canvas.width = canvas.clientWidth;
canvas.height = canvas.clientHeight;

const backgroundImage = new Image();
const backgroundSrc = '<?php echo ROOT_THEME_URL; ?>/background.webp?nocache=<?php echo time(); ?>';
backgroundImage.src = backgroundSrc;

let isEditingMode = false;
const squareSize = 10;
let scale = 1, offsetX = 0, offsetY = 0;
const zoomFactor = 1.05, minScale = 1, maxScale = 20;
let needsRedraw = false;

backgroundImage.onload = drawBackgroundAndGrid;

const jsonFilePath = "<?php echo ROOT_THEME_URL.'/data.json?nocache='.time(); ?>";

let reservedAreas = [];

async function loadJsonData() {
    try {
        const response = await fetch(jsonFilePath);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();
        reservedAreas = data || [];
        console.log("Loaded Data:", reservedAreas);
        requestRedraw();
    } catch (error) {
        console.error("Error loading JSON file:", error);
        reservedAreas = [];
    }
}

loadJsonData();

const modeToggleBtn = document.getElementById('mode-toggle');
const modeInfo = document.getElementById('mode-info');

function drawReservedAreas() {
    if (!reservedAreas.length) return;

    ctx.save();
    ctx.translate(offsetX, offsetY);
    ctx.scale(scale, scale);

    ctx.strokeStyle = 'rgba(255, 0, 0, 0.7)';
    ctx.fillStyle = 'rgba(255, 0, 0, 0.3)';
    ctx.lineWidth = Math.max(0.5 / scale, 0.2); // Adjust stroke width for visibility

    reservedAreas.forEach(area => {
        ctx.fillRect(area.startX, area.startY, area.width, area.height);
        ctx.strokeRect(area.startX, area.startY, area.width, area.height);
    });

    ctx.restore();
}


// function isOverReservedArea(x, y) {
//     const adjustedX = (x - offsetX) / scale;
//     const adjustedY = (y - offsetY) / scale;

//     return reservedAreas.find(area =>
//         adjustedX >= area.startX && adjustedX < area.startX + area.width &&
//         adjustedY >= area.startY && adjustedY < area.startY + area.height
//     );
// }

function isOverReservedArea(x, y) {
    return reservedAreas.find(area =>
        x >= area.startX && x < area.startX + area.width &&
        y >= area.startY && y < area.startY + area.height
    );
}


let selectedX = -1, selectedY = -1;
function handleCanvasClick(event) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    // Convert click position to canvas space BEFORE applying scale and offset
    let clickX = (event.clientX - rect.left) * scaleX;
    let clickY = (event.clientY - rect.top) * scaleY;

    // Adjust for zoom and panning (inverse transform)
    let adjustedX = (clickX - offsetX) / scale;
    let adjustedY = (clickY - offsetY) / scale;

    // Snap to grid
    selectedX = Math.floor(adjustedX / squareSize) * squareSize;
    selectedY = Math.floor(adjustedY / squareSize) * squareSize;

    if (isEditingMode) {
        if (!isOverReservedArea(selectedX, selectedY)) {
            requestRedraw();

            ctx.save();
            ctx.translate(offsetX, offsetY);
            ctx.scale(scale, scale);

            ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
            ctx.fillRect(selectedX, selectedY, squareSize, squareSize);

            ctx.restore();

            document.getElementById('bottom-bar').style.display = 'block';
        } else {
            alert('Cannot edit reserved area.');
        }
    } else {
        const area = isOverReservedArea(selectedX, selectedY);
        if (area) showBusinessCard(area);
    }
}


function toggleMode() {
    isEditingMode = !isEditingMode;
    requestRedraw();

    canvas.classList.toggle('editing', isEditingMode);

    modeToggleBtn.textContent = isEditingMode ? 'Switch to Viewing Mode' : 'Switch to Editing Mode';
    modeInfo.textContent = isEditingMode
        ? 'Editing Mode: Select a rectangular area to claim.'
        : 'Viewing Mode: Click on reserved areas for details.';
}

canvas.addEventListener('click', handleCanvasClick);
modeToggleBtn.addEventListener('click', toggleMode);


function drawBackgroundAndGrid() {
    scale = Math.max(minScale, Math.min(maxScale, scale));
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();

    ctx.translate(offsetX, offsetY);
    ctx.scale(scale, scale);
    
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

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
    
    ctx.restore();
}

function requestRedraw() {
    drawBackgroundAndGrid();
}

canvas.addEventListener('wheel', (event) => {
    event.preventDefault();
    const rect = canvas.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    const delta = event.deltaY < 0 ? zoomFactor : 1 / zoomFactor;
    const newScale = scale * delta;

    if (newScale >= minScale && newScale <= maxScale) {
        offsetX = mouseX - (mouseX - offsetX) * (newScale / scale);
        offsetY = mouseY - (mouseY - offsetY) * (newScale / scale);
        scale = newScale;
        requestRedraw();
    }
}, { passive: false });

document.addEventListener('keydown', (event) => {
    if (event.ctrlKey && (event.key === '+' || event.key === '-')) {
        event.preventDefault();
        scale *= event.key === '+' ? zoomFactor : 1 / zoomFactor;
        requestRedraw();
    }
    if (event.ctrlKey && event.key === '0') {
        event.preventDefault();
        scale = 1;
        requestRedraw();
    }
});


// dragging, panning

let isDragging = false;
let lastMouseX = 0;
let lastMouseY = 0;


canvas.addEventListener("mousedown", (event) => {
    isDragging = true;
    lastMouseX = event.clientX;
    lastMouseY = event.clientY;
});

canvas.addEventListener("mousemove", (event) => {
    if (!isDragging) return;

    let dx = event.clientX - lastMouseX;
    let dy = event.clientY - lastMouseY;

    offsetX += dx;
    offsetY += dy;

    lastMouseX = event.clientX;
    lastMouseY = event.clientY;

    requestRedraw(); // Redraw with new offset
});

canvas.addEventListener("mouseup", () => {
    isDragging = false;
});

canvas.addEventListener("mouseleave", () => {
    isDragging = false;
});


// MOBILE

let lastTouchDistance = 0;
let isTouchPanning = false;
let lastTouchX = 0, lastTouchY = 0;

canvas.addEventListener("touchstart", (event) => {
    if (event.touches.length === 2) {
        // Pinch zoom start
        lastTouchDistance = getTouchDistance(event.touches);
    } else if (event.touches.length === 1) {
        // Start panning
        isTouchPanning = true;
        lastTouchX = event.touches[0].clientX;
        lastTouchY = event.touches[0].clientY;
    }
});

canvas.addEventListener("touchmove", (event) => {
    event.preventDefault(); // Prevent scrolling

    if (event.touches.length === 2) {
        // Handle pinch zoom
        const newDistance = getTouchDistance(event.touches);
        const delta = newDistance / lastTouchDistance;
        lastTouchDistance = newDistance;

        const rect = canvas.getBoundingClientRect();
        const centerX = (event.touches[0].clientX + event.touches[1].clientX) / 2 - rect.left;
        const centerY = (event.touches[0].clientY + event.touches[1].clientY) / 2 - rect.top;

        const newScale = scale * delta;

        if (newScale >= minScale && newScale <= maxScale) {
            offsetX = centerX - (centerX - offsetX) * (newScale / scale);
            offsetY = centerY - (centerY - offsetY) * (newScale / scale);
            scale = newScale;
            requestRedraw();
        }
    } else if (isTouchPanning && event.touches.length === 1) {
        // Handle panning
        const dx = event.touches[0].clientX - lastTouchX;
        const dy = event.touches[0].clientY - lastTouchY;
        offsetX += dx;
        offsetY += dy;
        lastTouchX = event.touches[0].clientX;
        lastTouchY = event.touches[0].clientY;
        requestRedraw();
    }
}, { passive: false });

canvas.addEventListener("touchend", () => {
    isTouchPanning = false;
    lastTouchDistance = 0;
});

// Helper function to calculate the distance between two touch points
function getTouchDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx * dx + dy * dy);
}


// POPUP
document.querySelectorAll('.close-popup').forEach(popup => {
  popup.addEventListener('click', event => {
    event.target.closest('.popup-overlay').style.display = 'none';
  });
});

function showBusinessCard(area) {
    let popup = document.querySelector('#business-info-popup').style.display = 'block';
    document.getElementById('business-twitter').textContent = `@${area.username}`;
    document.getElementById('business-twitter').href = `https://x.com/${area.username}`;

    const twitterImageContainer = document.querySelector('#business-info-popup .twitter-image');
    twitterImageContainer.innerHTML = '';
    
    const twitterImage = document.createElement('img');
    twitterImage.src = `https://unavatar.io/twitter/${area.username}`;
    twitterImageContainer.appendChild(twitterImage);
}

function closePopup(area) {
    document.querySelector('#business-info-popup').style.display = 'block';
}


</script>