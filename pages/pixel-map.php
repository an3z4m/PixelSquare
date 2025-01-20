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

        #mode-toggle {
            background-color: #f60;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        #mode-toggle:hover {
            background-color: #e55;
        }

        #bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            display: none;
            padding: 15px;
            text-align: center;
        }

        #bottom-bar p {
            margin: 0;
            font-size: 16px;
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
            color: #000;
            padding: 20px;
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        #popup-content h2 {
            margin-top: 0;
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
    <button id="mode-toggle">Switch to Viewing Mode</button>
    <canvas id="grid-canvas" width="1000" height="1000"></canvas>

    <div id="info">
        <p id="mode-info">Editing Mode: Select a rectangular area to claim.</p>
    </div>

    <div id="bottom-bar">
        <p id="price-info"></p>
        <button id="upload-image">Upload Image</button>
    </div>

    <div id="popup-overlay">
        <div id="popup-content">
            <h2 id="business-name">Business Name</h2>
            <p id="business-email">Email: example@example.com</p>
            <p id="business-twitter">Twitter: @example</p>
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
        const squareSize = 10;
        const modeToggleBtn = document.getElementById('mode-toggle');
        const modeInfo = document.getElementById('mode-info');
        let isEditingMode = true;


        function drawGrid() {
            ctx.strokeStyle = 'rgba(0, 255, 0, 1)';
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

        function drawReservedAreas() {
            reservedAreas.forEach(area => {
                ctx.fillStyle = 'rgba(0, 0, 255, 0.2)';
                ctx.fillRect(area.startX, area.startY, area.width, area.height);
                ctx.strokeStyle = '#ccc';
                ctx.strokeRect(area.startX, area.startY, area.width, area.height);
            });
        }

        function isOverReservedArea(x, y) {
            return reservedAreas.find(area =>
                x >= area.startX && x < area.startX + area.width &&
                y >= area.startY && y < area.startY + area.height
            );
        }

        function handleCanvasClick(event) {
            const rect = canvas.getBoundingClientRect();
            const clickedX = Math.floor((event.clientX - rect.left) / squareSize) * squareSize;
            const clickedY = Math.floor((event.clientY - rect.top) / squareSize) * squareSize;

            if (isEditingMode) {
                if (!isOverReservedArea(clickedX, clickedY)) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    drawGrid();
                    drawReservedAreas();
                    ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
                    ctx.fillRect(clickedX, clickedY, squareSize, squareSize);
                } else {
                    alert('Cannot edit reserved area.');
                }
            } else {
                const area = isOverReservedArea(clickedX, clickedY);
                if (area) {
                    showBusinessCard(area);
                }
            }
        }

        
        function toggleMode() {
            isEditingMode = !isEditingMode;
            modeToggleBtn.textContent = isEditingMode ? 'Switch to Viewing Mode' : 'Switch to Editing Mode';
            modeInfo.textContent = isEditingMode ? 'Editing Mode: Select a rectangular area to claim.' : 'Viewing Mode: Click on reserved areas for details.';
        }

        document.getElementById('close-popup').addEventListener('click', () => {
            document.getElementById('popup-overlay').style.display = 'none';
        });

        canvas.addEventListener('click', handleCanvasClick);
        modeToggleBtn.addEventListener('click', toggleMode);

        
        function showBusinessCard(area) {
            document.getElementById('popup-overlay').style.display = 'block';
            document.getElementById('business-name').textContent = area.name;
            document.getElementById('business-email').textContent = `Email: ${area.email}`;
            document.getElementById('business-twitter').textContent = `Twitter: ${area.twitter}`;
        }


        drawGrid();
        drawReservedAreas();
    </script>
</body>
</html>