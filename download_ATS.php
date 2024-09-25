<?php
require_once "functions.php";
session_start();

// Directory where ATS modpacks are stored
$modpacksDir = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/modpacks/American Truck Simulator/";

// Check if the file parameter is set in the URL
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filePath = $modpacksDir . $file;

    // Check if the file exists
    if (file_exists($filePath)) {
        // Notify on file download
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            $username = $_SESSION["username"];
            $message = "<@485553416521908255> User **$username** has just downloaded **$file** from ATS.";
            $response = sendDiscordNotification($message);

            // Check response and log errors
            if (!$response) {
                error_log("Failed to send Discord notification for file $file", 3, 'webhook_log.txt');
            } else {
                error_log("Discord notification sent successfully for file $file", 3, 'webhook_log.txt');
            }
        }

        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Get the file size
        $fileSize = filesize($filePath);

        // Set appropriate headers
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$file\"");
        header("Content-Length: " . $fileSize);

        // Read the file
        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
        error_log("File not found: $file", 3, 'webhook_log.txt');
    }
} else {
    echo "Invalid request. Missing parameters.";
    error_log("Invalid request: Missing file parameter", 3, 'webhook_log.txt');
}
?>
