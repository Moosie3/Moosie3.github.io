<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Directory where your files are stored
$directory = "D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/Database";

// Check if 'file' parameter is present
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    $filePath = $directory . '/' . $file;

    // Check if the file exists and is a file
    if (is_file($filePath)) {
        // Delete the file
        if (unlink($filePath)) {
            echo "File '$file' has been deleted.";
        } else {
            echo "Error deleting file '$file'.";
        }
    } else {
        echo "File '$file' does not exist.";
    }
} else {
    echo "No file specified.";
}

// Redirect back to the main screen
header("Location: fileshare.php");
exit;
?>
