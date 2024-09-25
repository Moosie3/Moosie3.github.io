<?php
// Get the file name from the query parameter
$file = isset($_GET['file']) ? $_GET['file'] : ''; 
$filePath = 'D:/FBowers/Server stuff/WEBHOST/Hostingtools/XAMPP/htdocs/Database/' . $file; 

// Check if the file exists
if (!file_exists($filePath)) {
    die('File does not exist: ' . $filePath);
}

// Determine the MIME type for the file
$mime_type = @mime_content_type($filePath);
if (!$mime_type) {
    $mime_type = 'video/mp4'; // Fallback MIME type
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Viewing Video</title>
    <style>
        body {
            margin: 0;
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        video {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <video controls>
        <source src="<?php echo 'Database/' . urlencode($file); ?>" type="<?php echo $mime_type; ?>">
        Your browser does not support the video tag.
    </video>
</body>
</html>
