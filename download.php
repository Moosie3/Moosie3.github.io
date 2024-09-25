// download.php
<?php
require_once "functions.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filePath = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/Database/" . $filename;

    if (file_exists($filePath)) {
        // Notify on file download
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            $message = "<@485553416521908255> User **$username** has just downloaded **$filename**.";
            sendDiscordNotification($message);
        }

        // Proceed with file download
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}
?>
