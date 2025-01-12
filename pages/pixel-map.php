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
            cursor: pointer;
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
        <p>Tap or click on a pixel to claim it.</p>
        <button id="clear-selection">Clear Selection</button>
    </div>

    <div id="bottom-bar">
        <p id="price-info">1 Pixel Selected</p>
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
        const canvas = document.getElementById('grid-canvas');
        const ctx = canvas.getContext('2d');
        const squareSize = 10; // Size of each pixel block
        let reservedAreas = []; // Reserved areas from JSON

        // Load reserved areas (JSON loading is omitted for brevity)
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
                ctx.fillStyle = 'rgba(0, 0, 255, 0.5)';
                ctx.fillRect(area.startX * squareSize, area.startY * squareSize, squareSize, squareSize);
                ctx.strokeStyle = '#ccc';
                ctx.strokeRect(area.startX * squareSize, area.startY * squareSize, squareSize, squareSize);
            });
        }

        function isSelectionValid(x, y) {
            return !reservedAreas.some(area => area.startX === x && area.startY === y);
        }

        function clearSelection() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawGrid();
            drawReservedAreas();
            document.getElementById('clear-selection').style.display = 'none';
            document.getElementById('bottom-bar').style.display = 'none';
        }

        function handleSelection(clientX, clientY) {
            const rect = canvas.getBoundingClientRect();
            const x = Math.floor((clientX - rect.left) / squareSize);
            const y = Math.floor((clientY - rect.top) / squareSize);

            if (isSelectionValid(x, y)) {

                const width = 50; //Math.abs(endX - startX) + 1;
                const height = 50; //Math.abs(endY - startY) + 1;
                const totalPrice = width * height;
                
                clearSelection();
                ctx.fillStyle = 'rgba(255, 0, 0, 0.7)';
                ctx.fillRect(x * squareSize, y * squareSize, squareSize, squareSize);

                document.getElementById('price-info').textContent = '1 Pixel Selected';
                document.getElementById('bottom-bar').style.display = 'block';
                document.getElementById('clear-selection').style.display = 'block';

                // Pass selection coordinates and size to the iframe
                const iframe = document.querySelector('#popup-content iframe');
                iframe.src = `<?php echo ROOT_THEME_URL; ?>/upload.php?startX=${x}&startY=${y}&width=${width}&height=${height}`;
              
            } else {
                alert('This pixel is already reserved!');
            }
        }

        canvas.addEventListener('mousedown', (event) => {
            handleSelection(event.clientX, event.clientY);
        });

        canvas.addEventListener('touchstart', (event) => {
            const touch = event.touches[0];
            handleSelection(touch.clientX, touch.clientY);
        });

        document.getElementById('clear-selection').addEventListener('click', clearSelection);

        drawGrid();
        drawReservedAreas();


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