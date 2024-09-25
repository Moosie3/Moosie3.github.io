<?php
require_once "config.php"; // Include the database configuration
require_once "functions.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

// Initialize username variable if set, otherwise set to Guest
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Guest";

// Check user permissions for file share access
$can_access_file_share = false;

// Prepare SQL to check access permission
$sql = "SELECT can_access_file_share FROM users WHERE username = ?";

// Check if the user is in the database and has access permissions
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

// Redirect to index.php if the user does not have permission
if (!$can_access_file_share) {
    header("Location: index.php");
    exit;
}

// Directory where your files are stored
$directory = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/Database";

// Get the search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Normalize the search query by replacing underscores, spaces, and apostrophes
$normalizedSearchQuery = str_replace(["_", " ", "'"], '', strtolower($searchQuery));

// Array to store files
$files = [];

// Check if directory exists and is readable
if (is_dir($directory) && is_readable($directory)) {
    // Open directory
    if ($handle = opendir($directory)) {
        // Read directory
        while (($file = readdir($handle)) !== false) {
            // Exclude current and parent directory
            if ($file != "." && $file != "..") {
                // Normalize the filename by replacing underscores, spaces, and apostrophes
                $normalizedFileName = str_replace(["_", " ", "'"], '', strtolower($file));

                // If a search query is provided, filter the files
                if ($normalizedSearchQuery === '' || stripos($normalizedFileName, $normalizedSearchQuery) !== false) {
                    $files[] = $file;
                }
            }
        }
        closedir($handle);
    }
}

// Function to shorten file name if necessary
function shortenFileName($fileName, $maxLength = 30) {
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileNameWithoutExtension = basename($fileName, "." . $extension);

    if (strlen($fileNameWithoutExtension) > $maxLength) {
        // Shorten file name
        $shortenedFileName = substr($fileNameWithoutExtension, 0, $maxLength) . '...' . $extension;
    } else {
        // Keep full file name
        $shortenedFileName = $fileName;
    }

    return $shortenedFileName;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Screen</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-image: url('images/wallpaper.png'); /* Path to your wallpaper */
            background-size: cover; /* Ensure the image covers the whole background */
            background-repeat: no-repeat; /* Prevent repeating the image */
            background-attachment: fixed; /* Fix the image in place when scrolling */
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff; /* Default text color for better readability on the image */
        }

        .main-screen {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8); /* Semi-transparent dark grey */
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        h2 {
            color: #9c27b0; /* Purple color for headings */
        }

        .file-list {
            background-color: #555; /* Dark grey background for file list */
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 1px solid #666;
            position: relative; /* Position relative to place dropdown absolutely */
        }

        .file-item .file-details {
            flex: 1;
            display: flex; /* Make file-details a flex container */
            align-items: center;
            overflow: hidden; /* Hide overflowed content */
            margin-right: 10px; /* Add some space between file name and dropdown */
        }

        .file-item .file-details h4 {
            margin: 0;
            color: #fff;
            white-space: nowrap; /* Prevent text from wrapping */
            overflow: hidden; /* Hide overflowed text */
            text-overflow: ellipsis; /* Show ellipsis when text overflows */
        }

        .file-item .actions {
            display: flex;
            align-items: center;
            flex-shrink: 0; /* Prevent the actions section from shrinking */
        }

        .dropdown {
            position: relative;
        }

        .dropbtn {
            background-color: #555;
            border: none;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dropbtn:hover {
            background-color: #777;
        }

        .dot {
            background-color: #fff;
            border-radius: 50%;
            height: 6px; /* Size of the dots */
            width: 6px; /* Size of the dots */
            margin: 2px; /* Space between dots */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #555;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #777;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .actions a {
            color: #fff; /* White text for links */
            text-decoration: none;
            padding: 8px 16px; /* Adjust padding as needed */
            background-color: #9c27b0; /* Purple button */
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #7b1fa2; /* Darker purple on hover */
        }

        .actions a:active {
            transform: translateY(1px);
        }

        /* Notification Style */
        .notification {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #4caf50; /* Green background */
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none; /* Hidden by default */
            z-index: 1000; /* Ensure it appears above other content */
            opacity: 0; /* Initial state for fade-in effect */
            transition: opacity 0.5s ease; /* Smooth fade-in/out transition */
        }

        .notification.show {
            display: block; /* Make visible */
            opacity: 1; /* Fully opaque */
        }
    </style>
</head>
<body>
    <div class="notification" id="notification">Link copied to clipboard!</div>

    <div class="main-screen">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

        <!-- Search Form -->
        <form method="get" action="">
            <input type="text" name="search" placeholder="Search files..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="actions">
            <a href="upload.php">Upload a File</a>
        </div>

        <div class="file-list">
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <div class="file-details">
                            <h4 title="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars(shortenFileName($file)); ?></h4>
                        </div>
                        <div class="actions">
                            <div class="dropdown">
                                <button class="dropbtn">
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                </button>
                                <div class="dropdown-content">
                                    <a href="download.php?file=<?php echo urlencode($file); ?>">Download</a>
                                    <a href="view.php?file=<?php echo urlencode($file); ?>" target="_blank">View</a>
                                    <a href="delete.php?file=<?php echo urlencode($file); ?>" onclick="return confirm('Are you sure you want to delete this file?');">Remove</a>
                                    <a href="VLClink.php?file=<?php echo urlencode($file); ?>" class="copy-vlc-link" data-file="<?php echo urlencode($file); ?>">VLC link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No files found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    showNotification('Link copied to clipboard!');
                }).catch(function() {
                    showNotification('Failed to copy link.');
                });
            }

            function showNotification(message) {
                const notification = document.getElementById('notification');
                notification.textContent = message;
                notification.classList.add('show');

                setTimeout(function() {
                    notification.classList.remove('show');
                }, 2000);
            }

            document.addEventListener('click', function(event) {
                if (event.target && event.target.matches('.copy-vlc-link')) {
                    event.preventDefault();
                    const file = event.target.getAttribute('data-file');
                    const url = `http://192.168.1.236/Database/${file}`;
                    copyToClipboard(url);
                }
            });
        });
    </script>
</body>
</html>
