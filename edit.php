<?php
session_start();

// Initialize $adminMode with a default value
$adminMode = false;

// Check if admin mode is enabled
if (isset($_SESSION['admin_mode']) && $_SESSION['admin_mode']) {
    $adminMode = true;
}

// Handle form submissions for updating marquee details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $availability = isset($_POST['availability']) ? $_POST['availability'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $ebayLink = isset($_POST['ebayLink']) ? trim($_POST['ebayLink']) : '';
    $originalTitle = isset($_POST['originalTitle']) ? trim($_POST['originalTitle']) : '';

    // Validate title to ensure no symbols, only alphanumeric and spaces
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
        $error = "Title can only contain letters, numbers, and spaces.";
    } elseif (!is_numeric($price)) {
        // Validate price to ensure it's a number
        $error = "Price must be a number.";
    } else {
        // Update marquee directory
        $uploadDir = 'Marquee/';
        $originalPath = $uploadDir . $originalTitle;
        $newPath = $uploadDir . $title;

        if ($originalTitle !== $title) {
            // Rename directory if title has changed
            rename($originalPath, $newPath);
        }

        // Update the info file
        $infoFile = $newPath . '/info.txt';
        $infoContent = "Title: $title\nAvailability: $availability\nPrice: $price\nDescription: $description\nEbay Link: $ebayLink\n";
        file_put_contents($infoFile, $infoContent);

        // Redirect to avoid resubmission on refresh
        header('Location: marqueeedit.php');
        exit;
    }
}

// Fetch marquee details for editing
$marqueeDir = 'Marquee/';
$marquees = [];

if (is_dir($marqueeDir)) {
    $directories = array_filter(glob($marqueeDir . '*'), 'is_dir');
    foreach ($directories as $directory) {
        $title = basename($directory);
        $thumbnail = $directory . '/thumbnail.jpg';
        $infoFile = $directory . '/info.txt';

        // Read the information file
        $info = [];
        if (file_exists($infoFile)) {
            $lines = file($infoFile);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, ': ') !== false) {
                    list($key, $value) = explode(': ', $line, 2);
                    $info[trim($key)] = trim($value);
                }
            }
        }

        // Add to the marquee list
        $marquees[] = [
            'title' => $title,
            'thumbnail' => file_exists($thumbnail) ? $thumbnail : 'path/to/default-thumbnail.jpg',
            'description' => isset($info['Description']) ? $info['Description'] : '',
            'ebayLink' => isset($info['Ebay Link']) ? $info['Ebay Link'] : '',
            'availability' => isset($info['Availability']) ? $info['Availability'] : 'Unknown',
            'price' => isset($info['Price']) ? $info['Price'] : 'N/A'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Marquees - Pinball Vault</title>
    <style>
        body {
            background-color: #2e2e2e;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .logo-title {
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 50px;
            margin-right: 15px;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
        }
        .header div {
            display: flex;
            align-items: center;
        }
        .header button {
            background-color: #444;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin: 0 5px;
        }
        .header button:hover {
            background-color: #555;
        }
        .upload-button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 5px;
        }
        .upload-button:hover {
            background-color: #666;
        }
        .content {
            padding: 20px;
            margin-top: 60px; /* To avoid overlap with the fixed header */
        }
        .marquee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .marquee-item {
            background-color: #444;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .marquee-item img {
            max-width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        .marquee-item .info {
            color: #e0e0e0;
            text-align: center;
        }
        .marquee-item .title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #007bff; /* Blue color for title */
            text-decoration: none;
            cursor: pointer;
            display: block;
        }
        .marquee-item .description {
            margin-bottom: 10px;
        }
        .marquee-item .available {
            color: #4caf50; /* Green color */
        }
        .marquee-item .out-of-stock {
            color: #f44336; /* Red color */
        }
        .marquee-item a {
            color: #007bff;
            text-decoration: none;
        }
        .marquee-item a:hover {
            text-decoration: underline;
        }
        .view-ebay {
            display: block;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
        }
        .view-ebay:hover {
            background-color: #0056b3;
        }
        .error {
            color: #f44336;
            margin: 10px 0;
        }
        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Adjust spacing between form elements */
        }
        form label {
            display: block;
            font-weight: bold;
            color: #e0e0e0; /* Ensures labels are visible against dark background */
        }
        form input[type="text"], form textarea, form select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
            background-color: #333; /* Matches form field background with page */
            border: 1px solid #444; /* Adds a border to the form fields */
            color: #e0e0e0; /* Ensures text is visible */
        }
        form input[type="text"]:focus, form textarea:focus, form select:focus {
            border-color: #007bff; /* Highlights form fields on focus */
            outline: none; /* Removes default outline */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-title">
            <div class="logo">
                <a href="https://www.example.com">
                    <img src="images/marqueelogo.png" alt="Pinball Vault Logo">
                </a>
            </div>
            <div class="title">Pinball Vault</div>
        </div>
        <div>
            <button onclick="location.href='marqueemenu.php'" class="upload-button">Menu</button>
            <?php if ($adminMode): ?>
                <button onclick="location.href='marqueeupload.php'" class="upload-button">Upload</button>
                <button onclick="location.href='marqueeedit.php'" class="upload-button">Edit</button>
                <button onclick="location.href='marqueelogout.php'">Logout</button>
            <?php else: ?>
                <button onclick="location.href='marqueelogin.php'" class="upload-button">Login</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <h2>Edit Marquees</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="marquee-grid">
            <?php if (empty($marquees)): ?>
                <div class="marquee-item">
                    <p>No marquees found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($marquees as $marquee): ?>
                    <div class="marquee-item">
                        <img src="<?= htmlspecialchars($marquee['thumbnail']) ?>" alt="<?= htmlspecialchars($marquee['title']) ?>">
                        <form method="post" action="marqueeedit.php">
                            <input type="hidden" name="originalTitle" value="<?= htmlspecialchars($marquee['title']) ?>">
                            <label>
                                Title:
                                <input type="text" name="title" value="<?= htmlspecialchars($marquee['title']) ?>" required>
                            </label>
                            <label>
                                Description:
                                <textarea name="description"><?= htmlspecialchars($marquee['description']) ?></textarea>
                            </label>
                            <label>
                                Availability:
                                <select name="availability">
                                    <option value="Available" <?= $marquee['availability'] === 'Available' ? 'selected' : '' ?>>Available</option>
                                    <option value="Out of stock" <?= $marquee['availability'] === 'Out of stock' ? 'selected' : '' ?>>Out of stock</option>
                                </select>
                            </label>
                            <label>
                                Price:
                                <input type="text" name="price" value="<?= htmlspecialchars($marquee['price']) ?>" required>
                            </label>
                            <label>
                                Ebay Link:
                                <input type="text" name="ebayLink" value="<?= htmlspecialchars($marquee['ebayLink']) ?>">
                            </label>
                            <button type="submit" name="update" style="background-color: #4caf50; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
