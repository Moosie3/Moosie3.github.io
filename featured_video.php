<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Featured Video</title>
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

        .header {
            position: fixed;
            top: 0;
            right: 0;
            padding: 10px;
            background-color: rgba(68, 68, 68, 0.8);
            border-bottom: 1px solid #666;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .header a {
            color: #fff; /* Changed to white */
            text-decoration: none;
            font-size: 16px;
            margin-left: 20px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: rgba(68, 68, 68, 0.8);
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .header a:hover {
            background-color: #7b1fa2;
        }

        .video-container {
            max-width: 800px;
            margin: 100px auto 20px;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8);
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            text-align: center;
        }

        .video-title {
            color: #fff; /* Changed to white */
            font-size: 24px;
            margin-bottom: 20px;
        }

        .video-player {
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Create Account</a>
        <?php endif; ?>
    </div>

    <div class="video-container">
        <h1 class="video-title">Driving Timelapse | Drammen [Norway] to Nuuk [Greenland] | Promods 1.70 | Euro Truck Simulator 2</h1>
        <iframe class="video-player" width="560" height="315" src="https://www.youtube.com/embed/7wxu2UAo_K8" 
            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
        </iframe>
    </div>
</body>
</html>
