<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Initialize username variable if set, otherwise set to Guest
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Guest";

// Directory where files will be uploaded
$uploadDirectory = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/Database/";

// Max file size (500 GB)
$maxFileSize = 500 * 1024 * 1024 * 1024; // in bytes

// Allowed file types
$allowedFileTypes = ['*']; // Allow all file types

// Error messages
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if files were uploaded
    if (isset($_FILES['files'])) {
        // Loop through uploaded files
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Replace spaces with underscores in the filename
            $file_name = str_replace(' ', '_', $file_name);

            // Check file size
            if ($file_size > $maxFileSize) {
                $errors[] = "$file_name exceeds maximum file size.";
                continue;
            }

            // Check file type
            if (!in_array('*', $allowedFileTypes)) {
                $errors[] = "Invalid file type for $file_name.";
                continue;
            }

            // Upload file
            if (empty($errors)) {
                $uploadPath = $uploadDirectory . basename($file_name);
                if (move_uploaded_file($file_tmp, $uploadPath)) {
                    // File uploaded successfully
                    $message = "<@485553416521908255> User **$username** has just uploaded **$file_name**.";
                    $response = sendDiscordNotification($message);

                    // Log webhook response
                    error_log("Discord notification response: $response", 3, 'webhook_log.txt');
                } else {
                    $errors[] = "Error uploading $file_name.";
                    error_log("Error uploading file $file_name", 3, 'webhook_log.txt');
                }
            }
        }
    }
}

// Function to format bytes into a human-readable format
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Function to send Discord notification
function sendDiscordNotification($message) {
    $webhookUrl = "https://discord.com/api/webhooks/1216835175019708526/fO-gKCvZgmIeIYEhgTcQFYKKFMMJAiEXlnCY97FygmzNfTdHPPoUGybEpMXm_oNZ-6Sm";
    
    $payload = json_encode([
        'content' => $message,
    ]);
    
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL error: $error_msg";
    }
    
    curl_close($ch);
    return $response;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Files</title>
    <link rel="icon" href="icon/pngtest.png" type="image/png">
    <link rel="stylesheet" href="css/style.css"> <!-- Include your main CSS file -->
    <style>
        body {
            background-image: url('images/wallpaper.png'); /* Background image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .upload-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8); /* Semi-transparent dark background */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.7);
        }
        h2 {
            color: #9c27b0; /* Purple color for headings */
            margin-bottom: 20px;
            text-align: center;
        }
        .upload-form {
            margin-top: 20px;
        }
        .upload-form label {
            display: block;
            margin-bottom: 10px;
            color: #ffffff; /* White for labels */
        }
        .upload-form input[type="file"] {
            margin-bottom: 10px;
            background-color: #333;
            color: #fff;
        }
        .upload-form input[type="submit"] {
            padding: 10px 20px;
            background-color: #9c27b0; /* Purple button */
            border: none;
            color: #fff; /* White text */
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .upload-form input[type="submit"]:hover {
            background-color: #7b1fa2; /* Darker purple on hover */
        }
        .errors {
            color: #ff6f6f; /* Bright red for error messages */
            margin-bottom: 20px;
        }
        .errors ul {
            list-style: none;
            padding: 0;
        }
        .errors li {
            margin-bottom: 10px;
        }
        .progress-container {
            margin-top: 20px;
        }
        .progress-bar-container {
            margin-bottom: 15px;
            background-color: #555;
            border-radius: 4px;
            padding: 5px;
        }
        .progress-text {
            color: #fff;
            font-size: 12px;
            margin-bottom: 5px; /* Space between text and progress bar */
            text-align: center;
        }
        .progress-bar {
            background-color: #9c27b0; /* Purple progress bar */
            height: 20px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions a {
            color: #fff; /* White link color */
            text-decoration: none;
            font-size: 16px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2>Upload Files</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="upload-form">
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <label for="files">Select files to upload (Max size: <?php echo formatBytes($maxFileSize); ?>)</label>
                <input type="file" name="files[]" id="files" multiple required>
                <input type="submit" value="Upload">
            </form>
        </div>

        <div id="progressContainer" class="progress-container" style="display:none;"></div>

        <div class="actions">
            <a href="fileshare.php">Back to Main Menu</a>
        </div>
    </div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);
        var progressContainer = document.getElementById('progressContainer');
        progressContainer.innerHTML = ''; // Clear any existing progress bars
        progressContainer.style.display = 'block'; // Show progress container

        var files = document.getElementById('files').files;

        // Loop through all files and upload each one
        Array.from(files).forEach(function(file, index) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);

            // Create a unique ID for the progress bar container
            var progressBarId = 'progress-bar-container-' + index;
            
            // Create a progress bar container
            var progressBarContainer = document.createElement('div');
            progressBarContainer.className = 'progress-bar-container';
            progressBarContainer.id = progressBarId;

            var progressText = document.createElement('div');
            progressText.className = 'progress-text';
            progressText.textContent = file.name + ' 0% (0 B/0 B)'; // Initial text

            var progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';

            progressBarContainer.appendChild(progressText);
            progressBarContainer.appendChild(progressBar);
            progressContainer.appendChild(progressBarContainer);

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = file.name + ' ' + Math.round(percentComplete) + '% (' + formatBytes(e.loaded) + '/' + formatBytes(e.total) + ')';
                }
            });

            xhr.upload.addEventListener('load', function() {
                progressBar.style.width = '100%';
                progressText.textContent = file.name + ' 100% (' + formatBytes(file.size) + '/' + formatBytes(file.size) + ')';
                // Remove the progress bar container after a short delay
                setTimeout(function() {
                    document.getElementById(progressBarId).remove();
                }, 2000); // Delay of 2 seconds before removal
            });

            xhr.upload.addEventListener('error', function() {
                progressText.textContent = file.name + ' Error';
            });

            xhr.upload.addEventListener('abort', function() {
                progressText.textContent = file.name + ' Aborted';
            });

            xhr.send(formData);
        });
    });

    // JavaScript function to format bytes
    function formatBytes(bytes, precision = 2) {
        var units = ['B', 'KB', 'MB', 'GB', 'TB'];
        var bytes = Math.max(bytes, 0);
        var pow = Math.floor((bytes ? Math.log(bytes) : 0) / Math.log(1024));
        pow = Math.min(pow, units.length - 1);
        bytes /= Math.pow(1024, pow);
        return bytes.toFixed(precision) + ' ' + units[pow];
    }
</script>
</body>
</html>
