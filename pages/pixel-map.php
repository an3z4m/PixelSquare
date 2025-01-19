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
const jsonFilePath = "<?php echo ROOT_THEME_URL.'/data.json?nocache='.time(); ?>";

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
        if (reservedAreas.length === 0) return;
        reservedAreas.forEach(area => {
            ctx.fillStyle = 'rgba(0,0,255,0.2)';
            ctx.fillRect(area.startX, area.startY, area.width, area.height);
            ctx.strokeStyle = '#ccc';
            ctx.strokeRect(area.startX, area.startY, area.width, area.height);
        });
    }

    // Check if the clicked square is in a reserved area
    function isSelectionValid(x, y) {
        for (const area of reservedAreas) {
            const withinX = x >= area.startX && x <= (area.startX + (area.width / squareSize));
            const withinY = y >= area.startY && y <= (area.startY + (area.height / squareSize));

            if (withinX && withinY) {
                return false; // Overlap detected
            }
        }
        return true;
    }

    // Handle click or touch events
    canvas.addEventListener('click', (event) => {
        const rect = canvas.getBoundingClientRect();
        const clickedX = Math.floor((event.clientX - rect.left) / squareSize);
        const clickedY = Math.floor((event.clientY - rect.top) / squareSize);

        if (isSelectionValid(clickedX, clickedY)) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawGrid();
            drawReservedAreas();

            // Highlight the selected square
            ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
            ctx.fillRect(clickedX * squareSize, clickedY * squareSize, squareSize, squareSize);

            // Display information about the selected square
            document.getElementById('price-info').textContent = `Selected Square: (${clickedX}, ${clickedY})`;
            document.getElementById('bottom-bar').style.display = 'block';

            // Pass selection coordinates and size to the iframe
            const iframe = document.querySelector('#popup-content iframe');
            width = 1;
            height = 1;
            iframe.src = `<?php echo ROOT_THEME_URL; ?>/upload.php?startX=${clickedX}&startY=${clickedY}&width=${width}&height=${height}`;

        } else {
            alert('Selection overlaps with a reserved area. Please try again.');
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


</script>
    
</body>
</html>
