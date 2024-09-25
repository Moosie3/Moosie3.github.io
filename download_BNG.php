<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php"; // Include database connection
require_once "functions.php"; // Include functions for Discord notification
session_start();

// Directory where BeamNG modpacks are stored
$modpacksDir = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/modpacks/BeamNG.drive/";

// Check if the file parameter is set in the URL
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Sanitize the file name
    $filePath = $modpacksDir . $file;

    // Debugging: Output the file path to check if it is correct
    echo "File path: " . htmlspecialchars($filePath) . "<br>";

    // Check if the file exists
    if (file_exists($filePath)) {
        // Notify on file download if user is logged in
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            $username = $_SESSION["username"];
            $message = "<@485553416521908255> User **$username** has just downloaded **$file** from BeamNG.";
            $response = sendDiscordNotification($message);

            // Check response and log errors
            if (strpos($response, "cURL error") !== false) {
                error_log("cURL error for file $file: $response", 3, 'webhook_log.txt');
            } else {
                error_log("Discord notification sent successfully for file $file", 3, 'webhook_log.txt');
            }
        }

        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set appropriate headers for file download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . urlencode($file) . "\"");
        header("Content-Length: " . filesize($filePath));

        // Read the file
        readfile($filePath);
        exit;
    } else {
        echo "File not found: " . htmlspecialchars($file);
        error_log("File not found: $filePath", 3, 'webhook_log.txt');
    }
} else {
    echo "Invalid request. Missing parameters.";
    error_log("Invalid request: Missing file parameter", 3, 'webhook_log.txt');
}
?>
