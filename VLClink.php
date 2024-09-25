<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Retrieve the file name from the query parameter
$file = isset($_GET['file']) ? $_GET['file'] : '';

// Validate and sanitize the file name
$file = htmlspecialchars(urldecode($file));

// Construct the URL
$url = "http://192.168.1.236/Database/" . $file;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View VLC Link</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-image: url('images/wallpaper.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8);
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            text-align: center;
        }
        .link {
            margin-top: 20px;
            font-size: 18px;
            word-wrap: break-word; /* Allow long URLs to wrap */
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #9c27b0;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .button:hover {
            background-color: #7b1fa2;
        }
        .tutorial {
            display: flex;
            align-items: flex-start;
            margin-top: 30px;
            text-align: left;
        }
        .tutorial img {
            width: 300px; /* Adjust size as needed */
            height: auto;
            margin-right: 20px; /* Space between image and text */
        }
        .tutorial .text {
            max-width: 500px; /* Limit width of the text area */
        }
        .tutorial-section {
            margin-bottom: 40px; /* Space between sections */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Copy this link:</h1>
        <p class="link"><?php echo $url; ?></p>
        <a href="main_screen.php" class="button">Back to Main Page</a>

        <!-- Desktop Tutorial Section -->
        <div class="tutorial-section">
            <h2>Desktop Tutorial</h2>
            <div class="tutorial">
                <img src="images/help1.png" alt="Desktop Tutorial Image 1">
                <div class="text">
                    <h3>How to Play the Video on Desktop</h3>
                    <p>1. Open VLC Media Player on your computer.</p>
                    <p>2. Click on the "Media" menu at the top left corner.</p>
                    <p>3. Select "Open Network Stream..." from the dropdown menu.</p>
                    <p>4. Copy and paste the link provided above into the input field.</p>
                    <p>5. Click "Play" to start watching the video.</p>
                </div>
            </div>
            <div class="tutorial">
                <img src="images/help2.png" alt="Desktop Tutorial Image 2">
                <div class="text">
                    <p>If you encounter any issues, contact me!</p>
                    <a href="https://discord.gg/UFt2EshtbG">Join my Discord!</a>
                </div>
            </div>
        </div>

        <!-- Mobile Tutorial Section -->
        <div class="tutorial-section">
            <h2>Mobile Tutorial</h2>
            <div class="tutorial">
                <img src="images/mobilehelp1.png" alt="Mobile Tutorial Image 1">
                <div class="text">
                    <h3>How to Play the Video on Mobile</h3>
                    <p>1. Copy the above link and open the VLC app on your mobile device.</p>
                    <p>2. Tap on "Network".</p>
                    <p>3. Select "Open Network Stream".</p>
                    <p>4. Copy and paste the link above into the input field.</p>
                    <p>5. Tap "Open Network Stream" to start watching the video.</p>
                </div>
            </div>
            <div class="tutorial">
                <img src="images/mobilehelp2.png" alt="Mobile Tutorial Image 2">
                <div class="text">
                    <p>If you encounter any issues, contact me!</p>
                    <a href="https://discord.gg/UFt2EshtbG">Join my Discord!</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>