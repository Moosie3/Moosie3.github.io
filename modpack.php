<?php
session_start();

// Define the directory structure and thumbnails
$games = [
    'Euro Truck Simulator 2' => 'ETS2.png',
    'American Truck Simulator' => 'ATS.png',
    'BeamNG.drive' => 'BNG.png'
];

// Base directory for modpacks
$baseDirectory = "modpacks";

// Handle directory selection
$selectedGame = isset($_GET['game']) ? $_GET['game'] : '';
$directory = $baseDirectory . '/' . $selectedGame;

$files = [];
$message = '';

if ($selectedGame && is_dir($directory)) {
    // Get files from the selected directory
    if ($handle = opendir($directory)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                $files[] = $file;
            }
        }
        closedir($handle);
    }
    if (empty($files)) {
        $message = "No modpack downloads available";
    }
} else {
    $message = "Invalid game selection or directory does not exist.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modpack Downloads</title>
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
            z-index: 1000; /* Ensure header is on top */
        }

        .header a {
            color: #fff; /* White text color */
            text-decoration: none;
            font-size: 16px;
            margin-left: 20px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: rgba(68, 68, 68, 0.8); /* Dark background */
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .header a:hover {
            background-color: #7b1fa2; /* Slightly lighter on hover */
        }

        .main-screen {
            max-width: 800px;
            margin: 100px auto 20px; /* Added top margin to account for fixed header */
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8);
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .game-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .game-item {
            text-align: center;
            width: 150px;
        }

        .game-item img {
            width: 128px;
            height: 128px;
            border-radius: 8px;
            border: 1px solid #666;
        }

        .game-item a {
            color: #fff; /* White text color */
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .game-item a:hover {
            text-decoration: underline;
        }

        .file-list {
            background-color: #555;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .file-item {
            padding: 10px 20px;
            border-bottom: 1px solid #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-item h4 {
            margin: 0;
            color: #fff; /* White text color */
        }

        .file-item a {
            color: #9c27b0;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #333;
            border-radius: 4px;
        }

        .file-item a:hover {
            background-color: #555;
        }

        .file-item a:active {
            transform: translateY(1px);
        }

        .no-files {
            color: #fff; /* White text color */
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Home</a>
        <a href="modpack.php">Modpack Downloads</a>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Create Account</a>
        <?php endif; ?>
    </div>

    <div class="main-screen">
        <h1>Modpack Downloads</h1>

        <!-- Game selection -->
        <div class="game-list">
            <?php foreach ($games as $dir => $thumbnail): ?>
                <div class="game-item">
                    <a href="?game=<?php echo urlencode($dir); ?>">
                        <img src="images/<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php echo htmlspecialchars($dir); ?> Thumbnail">
                        <span><?php echo htmlspecialchars($dir); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- File list -->
        <?php if ($selectedGame && !empty($files)): ?>
            <div class="file-list">
                <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <h4><?php echo htmlspecialchars($file); ?></h4>
                        <?php
                            // Define the download script based on the selected game
                            $downloadScript = '';
                            switch ($selectedGame) {
                                case 'Euro Truck Simulator 2':
                                    $downloadScript = 'download_ETS2.php';
                                    break;
                                case 'American Truck Simulator':
                                    $downloadScript = 'download_ATS.php';
                                    break;
                                case 'BeamNG.drive':
                                    $downloadScript = 'download_BNG.php';
                                    break;
                                default:
                                    $downloadScript = 'download.php'; // Fallback
                                    break;
                            }
                        ?>
                        <a href="<?php echo htmlspecialchars($downloadScript); ?>?file=<?php echo urlencode($file); ?>">Download</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($selectedGame): ?>
            <p class="no-files"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
