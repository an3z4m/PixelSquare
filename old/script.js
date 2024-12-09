// Function to create the 1000x1000 grid
var basePX = 100; // 1000
function createGrid() {
    try {
      const container = document.getElementById('grid-container');
      for (let i = 0; i < basePX * basePX; i++) {
        // for (let i = 0; i < 1000000; i++) {
            const square = document.createElement('div');
        square.classList.add('square');
        square.addEventListener('click', toggleSquareColor);
        container.appendChild(square);
      }
      return 0; // Success
    } catch (error) {
      console.error('Error creating grid:', error);
      return -1; // Error code
    }
  }
  
  // Toggle square color between grey and green
  function toggleSquareColor(event) {
    const square = event.target;
    if (square.style.backgroundColor === 'green') {
      square.style.backgroundColor = 'grey';
    } else {
      square.style.backgroundColor = 'green';
    }
  }
  
  // Zoom functionality
  function setupZoom() {
    try {
      const zoomSlider = document.getElementById('zoom-slider');
      const gridContainer = document.getElementById('grid-container');
      zoomSlider.addEventListener('input', (event) => {
        const scaleValue = event.target.value;
        gridContainer.style.transform = `scale(${scaleValue})`;
      });
      return 0; // Success
    } catch (error) {
      console.error('Error setting up zoom:', error);
      return -1; // Error code
    }
  }
  
  // Initialize grid and zoom functionality
  if (createGrid() === 0 && setupZoom() === 0) {
    console.log('Grid and zoom setup successful');
  } else {
    console.error('An error occurred during setup');
  }
  