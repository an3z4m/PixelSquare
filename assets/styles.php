<style>

body {
    background-color: #002;
    color: #fff;
    font-family: Arial, sans-serif;
    text-align: center;
    /* touch-action: none; */

}

body:has(canvas.editing) {
      /* height: 100vh;
      margin: 0; */
      background-color: #004;
}

/* canvas {
    border: 1px solid #ccc;
    image-rendering: pixelated;
    background-position: center;
    background-size: cover;
    background-image: url('<?php echo ROOT_THEME_URL; ?>/background.webp?nocache=<?php echo time(); ?>');
} */

canvas {
    border: 1px solid #ccc;
    image-rendering: pixelated;
    /* Enables both horizontal and vertical panning */
    /* touch-action: pan-x pan-y !important;  */
}

canvas.editing{   cursor: crosshair; }
canvas{   cursor: pointer; }

@media (max-width: 768px) {
    canvas {
        width: 100%;
        height: auto;
    }
}

#info {
    margin-top: 20px;
}

/* #mode-toggle {
    display: block;
    margin: auto;
    background-color: #f60;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    margin-bottom: 10px;
} */

/* #mode-toggle:hover {
    background-color: #e55;
} */

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

.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 20;
}

.popup-content {
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

.popup-content button.close-popup {
    background-color: #f60;
    color: #fff;
    border: none;
    padding: 5px 7px;
    cursor: pointer;
    border-radius: 100%;
    position: absolute;
    right: -25px;
    top: -25px;
}

.popup-overlay.wide .popup-content {
    padding: 10px;
    width: 70%;
    height: 80%;
    max-width: 500px;
    max-height: 500px;
}

.popup-overlay.wide iframe {
    width: 100%;
    display: block;
    height: 100%;
    border: none;
}


.popup-content h2 {
    margin-top: 0;
}


.popup-content .close-popup:hover {
    background-color: #e55;
}


@media (min-width: 768px) {
    canvas {
        width: 100;
        height: 100%;
    }
    .main {
        display: flex;
        justify-content: center;
        align-content: center;
        align-items: center;
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    canvas {
        width: 100%;
        height: auto;
    }

    .main {
        display: flex;
        justify-content: center;
        align-content: center;
        width: 100%;
        align-items: center;
        height: 100vh;
        flex-wrap: wrap;
    }
}

</style>
<style>

ul.menu {
    list-style: none;
    width: 100%;
    margin: 0;
    margin-bottom: 3px;
    padding: 0;
    display: flex;
    justify-content: center;
}

.menu li {
    margin-left: 10px;
    /* background: #000; */
    padding: 3px 8px;
    border-radius: 5px;
}

.menu a {
    text-decoration: none;
    color: #fff;
    font-size: 16px;
    cursor: pointer;

}

.menu li:hover {
    background: #f60;
}

.username { font-weight: bold; }

.profile-link, .logout-link, .login-link, .signup-link {
    font-size: 16px;
}

/* Mobile-friendly design */
@media (max-width: 600px) {
    .menu {
        flex-direction: column;
        align-items: flex-start;
    }

    .menu li {
        margin-left: 0;
        margin-bottom: 10px;
    }
}
</style>



<style>


    .mode-switcher {
      position: fixed;
      top: 10px;
      right: 10px;
    /* }
    .mode-switcher{ */
    /* position: relative; */
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100px;
      height: 40px;
      background-color: #e0e0e0;
      border-radius: 20px;
      padding: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
    }

    .switcher-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      height: 30px;
      width: 40px;
      background-color: #fff;
      border-radius: 50px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }

    .mode-text {
      z-index: 1;
      font-size: 16px;
      font-weight: bold;
      color: #666;
      display: flex;
      align-items: center;
    }

    .mode-text.editing {
      margin-left: 10px;
    }

    .mode-text.viewing {
      margin-right: 10px;
    }

    .editing .switcher-button {
      transform: translate(60px, -50%);
    }

    .editing .mode-text.editing {
      color: #0288d1;
    }

    .viewing .mode-text.viewing {
      color: #4caf50;
    }

    .editing .mode-switcher {
      background-color: #bbdefb;
    }

    .viewing .mode-switcher {
      background-color: #c8e6c9;
    }

    .icon {
      font-size: 18px;
      margin: 10px;
    }

    @media (max-width: 480px) {
      .mode-switcher {
        width: 80px;
        height: 35px;
      }

      .switcher-button {
        width: 35px;
        height: 25px;
      }

      .mode-text {
        font-size: 14px;
      }

      .editing .switcher-button {
        transform: translate(40px, -50%);
      }
    }
  </style>



<style>
       .twitter-image {
            margin: 20px auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
        }
        .twitter-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-links a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .profile-links a:hover {
            background-color: #0056b3;
        }
</style>