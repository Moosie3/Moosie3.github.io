<?php
session_start();

// Check if admin mode is enabled
if (!isset($_SESSION['admin_mode']) || !$_SESSION['admin_mode']) {
    header('Location: marqueemenu.php');
    exit();
}

// Check if a title was provided
if (!isset($_POST['title']) || empty($_POST['title'])) {
    header('Location: marqueemenu.php');
    exit();
}

// Sanitize the title
$title = basename($_POST['title']);

// Directory to delete
$marqueeDir = 'Marquee/' . $title;

// Check if the directory exists and delete it
if (is_dir($marqueeDir)) {
    // Remove all files and subdirectories
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($marqueeDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    rmdir($marqueeDir);
}

// Redirect back to the menu page
header('Location: marqueemenu.php');
exit();
?>
