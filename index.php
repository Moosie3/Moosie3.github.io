<?php
session_start();
require_once "config.php"; // Ensure this file contains your database connection

// Initialize variables
$can_access_file_share = false;

// Check if the user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Fetch user data from the database
    $username = $_SESSION["username"];
    
    // Prepare the SQL statement
    $sql = "SELECT can_access_file_share FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $can_access_file_share);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Log SQL preparation error
        error_log("SQL preparation error: " . mysqli_error($link), 3, 'error_log.txt');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Fin's Website</title>
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
            color: #fff; /* Button text color */
            text-decoration: none;
            font-size: 16px;
            margin-left: 20px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: rgba(68, 68, 68, 0.8); /* Dark button background */
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .header a:hover {
            background-color: #7b1fa2; /* Slightly lighter on hover */
        }

        .logout-button {
            color: #fff; /* Button text color */
            background-color: rgba(68, 68, 68, 0.8); /* Dark button background */
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #7b1fa2; /* Slightly lighter on hover */
        }

        .main-menu {
            max-width: 800px;
            margin: 100px auto 20px; /* Added top margin to account for fixed header */
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8);
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            text-align: center;
        }

        .main-menu h1 {
            color: #fff; /* Changed to white */
        }

        .menu-item {
            margin: 15px 0;
        }

        .menu-item a {
            color: #fff; /* Button text color */
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: rgba(68, 68, 68, 0.8); /* Dark button background */
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .menu-item a:hover {
            background-color: #7b1fa2; /* Slightly lighter on hover */
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
            <a class="logout-button" href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Create Account</a>
        <?php endif; ?>
    </div>

    <div class="main-menu">
        <h1>Welcome to Fin's Website</h1>
        <div class="menu-item">
            <a href="modpack.php">Modpack Downloads</a>
        </div>
        <div class="menu-item">
            <a href="https://discord.gg/UFt2EshtbG" target="_blank">Join My Discord</a>
        </div>
        <?php if ($can_access_file_share): ?>
            <div class="menu-item">
                <a href="fileshare.php">Random File Share</a>
            </div>
        <?php endif; ?>
        <div class="menu-item">
            <a href="featured_video.php">Featured Video</a>
        </div>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["username"] === "Local_Floof"): ?>
            <div class="menu-item">
                <a href="settings.php">Settings</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
