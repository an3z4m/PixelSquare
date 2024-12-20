<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Your Pixels</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        canvas {
            border: 1px solid #ccc;
            image-rendering: pixelated;
            cursor: crosshair;
            background-position: center;
            background-size: cover;
            background-image: url('<?php echo ROOT_THEME_URL; ?>/background.webp?nocache=<?php echo time(); ?>');
        }

        #info {
            margin-top: 20px;
        }

        #clear-selection {
            display: none;
            margin: 5px;
        }

        /* Bottom bar styles */
        #bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            display: none;
            padding: 15px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.5);
            z-index: 10;
            text-align: center;
        }

        #bottom-bar p {
            margin: 0;
            font-size: 16px;
            display: inline-block;
        }

        #bottom-bar button {
            background-color: #f60;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        #bottom-bar button:hover {
            background-color: #e55;
        }

        /* Popup styles */
        #popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 20;
        }

        #popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            width: 90%;
            height: 90%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        #popup-content iframe {
            width: 100%;
            height: 80%;
            border: none;
        }

        #popup-content button {
            background-color: #f60;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        #popup-content button:hover {
            background-color: #e55;
        }
    </style>
</head>
<body>
    <h1>Claim Your Pixels</h1>
    <canvas id="grid-canvas" width="1000" height="1000"></canvas>

    <div id="info">
        <p>Select a rectangular area to claim.</p>
        <button id="clear-selection">Clear Selection</button>
    </div>

    <div id="bottom-bar">
        <p id="price-info"></p>
        <button id="upload-image">Upload Image</button>
    </div>

    <div id="popup-overlay">
        <div id="popup-content">
            <h2>Upload Your Image</h2>
            <iframe src="<?php echo ROOT_THEME_URL.'/upload.php'; ?>"></iframe>
            <button id="close-popup">Close</button>
        </div>
    </div>

    <script>
        // Path to the JSON file
const jsonFilePath = "<?php echo ROOT_THEME_URL.'/data.json'; ?>";

var reservedAreas;
// Function to load and process the JSON file
async function loadJsonData() {
    try {
        // Fetch the JSON file
        const response = await fetch(jsonFilePath);

        // Check if the fetch was successful
        if (!response.ok) {
            console.log(`HTTP error! Status: ${response.status}`);
            return [];
        }

        // Parse the JSON data
        const data = await response.json();

        // Process or display the data
        console.log("Loaded Data:", data);
        reservedAreas = data;

        // Example: Iterating through rows
        data.forEach((row, index) => {
            console.log(`Row ${index + 1}:`, row);
        });
        return data;
    } catch (error) {
        console.error("Error loading JSON file:", error);
        return [];
    }
}

reservedAreas = loadJsonData();

console.log("reservedAreas:"+reservedAreas);
if(!reservedAreas || reservedAreas.length == undefined) reservedAreas = [];

    </script>
    <script>
        const canvas = document.getElementById('grid-canvas');
        const ctx = canvas.getContext('2d');
        const squareSize = 10; // Each "pixel" block size

        let isDragging = false;
        let startX, startY, endX, endY;
    
        // Load the background image
        // const background = new Image();
        // background.src = '<?php echo ROOT_THEME_URL; ?>/background.webp';
    
        // background.onload = () => {
        //     ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
        //     drawGrid();
        //     drawReservedAreas();
        // };
    
        drawGrid();
        drawReservedAreas();

        // Draw a 100x100 grid on top of the background
        function drawGrid() {
            ctx.strokeStyle = 'rgba(0, 255, 0, 1)'; // Light white grid lines
            ctx.lineWidth = 0.2;
    
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
        }
    
        // Draw reserved areas on the canvas
        function drawReservedAreas() {
            console.log("reservedAreas.length;"+reservedAreas.length);
            if(reservedAreas.length == 0) return;
            reservedAreas.forEach(area => {
                ctx.fillStyle = 'rgba(0,0,255,0.5)';
                ctx.fillRect(area.startX * squareSize, area.startY * squareSize, area.width, area.height);
                ctx.strokeStyle = '#ccc';
                ctx.strokeRect(area.startX * squareSize, area.startY * squareSize, area.width, area.height);
            });
        }
    
        // Check if the selection overlaps with reserved areas
        function isSelectionValid(startX, startY, endX, endY) {
            for (const area of reservedAreas) {

                const overlapX = Math.max(startX, area.startX) <= Math.min(endX, parseInt(area.startX) + parseInt(area.width));
                const overlapY = Math.max(startY, area.startY) <= Math.min(endY, parseInt(area.startY) + parseInt(area.height));

                if (overlapX && overlapY) {
                    return false; // Overlap detected
                }
            }
            return true;
        }
    
        // Draw selection rectangle
        function drawSelectionRectangle() {
            if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                const left = Math.min(startX, endX);
                const top = Math.min(startY, endY);
                const width = Math.abs(endX - startX) + 1;
                const height = Math.abs(endY - startY) + 1;
    
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.strokeRect(left * squareSize, top * squareSize, width * squareSize, height * squareSize);
            }
        }
    
        // Clear selection
        function clearSelection() {
            // ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
            drawGrid();
            drawReservedAreas();
            document.getElementById('clear-selection').style.display = 'none';
            document.getElementById('bottom-bar').style.display = 'none';
            startX = startY = endX = endY = undefined;
        }
    
        // Handle mouse events
        canvas.addEventListener('mousedown', (event) => {
            clearSelection();
            isDragging = true;
    
            const rect = canvas.getBoundingClientRect();
            startX = Math.floor((event.clientX - rect.left) / squareSize);
            startY = Math.floor((event.clientY - rect.top) / squareSize);
        });
    
        canvas.addEventListener('mousemove', (event) => {
            if (isDragging) {
                const rect = canvas.getBoundingClientRect();
                endX = Math.floor((event.clientX - rect.left) / squareSize);
                endY = Math.floor((event.clientY - rect.top) / squareSize);
    
                // ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
                // Clear the canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                drawGrid();
                drawReservedAreas();
                drawSelectionRectangle();
            }
        });
    
        canvas.addEventListener('mouseup', () => {
            isDragging = false;
    
            if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                const left = Math.min(startX, endX) * 10;
                const top = Math.min(startY, endY) * 10;
                const right = Math.max(startX, endX) * 10;
                const bottom = Math.max(startY, endY) * 10;

                console.log(left, top, right, bottom);

                if (isSelectionValid(left, top, right, bottom)) {
                    const width = Math.abs(endX - startX) + 1;
                    const height = Math.abs(endY - startY) + 1;
                    const totalPrice = width * height;
    
                    document.getElementById('price-info').textContent = `Total Price: $${totalPrice}`;
                    document.getElementById('bottom-bar').style.display = 'block';
                    document.getElementById('clear-selection').style.display = 'block';
    
                    // Pass selection coordinates and size to the iframe
                    const iframe = document.querySelector('#popup-content iframe');
                    iframe.src = `<?php echo ROOT_THEME_URL; ?>/upload.php?startX=${left}&startY=${top}&width=${width}&height=${height}`;
                } else {
                    alert('Selection overlaps with a reserved area. Please try again.');
                    clearSelection();
                }
            }
        });
    
        // Show upload popup
        document.getElementById('upload-image').addEventListener('click', () => {
            document.getElementById('popup-overlay').style.display = 'block';
        });
    
        // Close upload popup
        document.getElementById('close-popup').addEventListener('click', () => {
            document.getElementById('popup-overlay').style.display = 'none';
        });
    
        // Clear selection button handler
        document.getElementById('clear-selection').addEventListener('click', clearSelection);
    </script>
    
</body>
</html>