<?php
session_start();

// Check if admin mode is enabled
$adminMode = isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'];

if (!$adminMode) {
    // Redirect if not admin mode
    header('Location: marqueemenu.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the uploaded file and form data
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $availability = isset($_POST['availability']) ? $_POST['availability'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $ebayLink = isset($_POST['ebayLink']) ? trim($_POST['ebayLink']) : '';

    // Validate title to ensure no symbols, only alphanumeric and spaces
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
        $error = "Title can only contain letters, numbers, and spaces.";
    } elseif (!is_numeric($price)) {
        // Validate price to ensure it's a number
        $error = "Price must be a number.";
    } elseif (!filter_var($ebayLink, FILTER_VALIDATE_URL)) {
        // Validate eBay link to be a valid URL
        $error = "eBay link must be a valid URL.";
    } else {
        // Handle file uploads
        $uploadDir = 'Marquee/';
        $uploadPath = $uploadDir . $title;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Paths for thumbnail and full image
        $thumbnailPath = $uploadPath . '/thumbnail.png';
        $fullImagePath = $uploadPath . '/full_image.png';

        // Placeholder path
        $placeholderPath = 'images/marqueedefaultplaceholder.png'; // Adjust path to your placeholder

        // Check if an image was uploaded
        if (!empty($_FILES['image']['tmp_name']) && getimagesize($_FILES['image']['tmp_name'])) {
            // Move uploaded file to thumbnail path
            if (move_uploaded_file($_FILES['image']['tmp_name'], $thumbnailPath)) {
                // Copy the thumbnail to full_image
                copy($thumbnailPath, $fullImagePath);
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            // Use placeholder image if no valid image uploaded
            copy($placeholderPath, $thumbnailPath);
            copy($placeholderPath, $fullImagePath);
        }

        // Create or update the info file
        $infoFile = $uploadPath . '/info.txt';
        $infoContent = "Title: $title\nAvailability: $availability\nPrice: $price\nDescription: $description\nEbay Link: $ebayLink\n";
        file_put_contents($infoFile, $infoContent);

        // Redirect to avoid resubmission on refresh
        header('Location: marqueemenu.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Marquee - Pinball Vault</title>
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
        .form-container {
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            margin: 0 auto;
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #555;
            background-color: #333;
            color: #e0e0e0;
            box-sizing: border-box;
        }
        .form-container button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .form-container button:hover {
            background-color: #666;
        }
        .error {
            color: #f44336;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-title">
            <div class="logo">
                <a href="https://www.ebay.co.uk/usr/tranceinyapantz" target="_blank" rel="noopener noreferrer">
                    <img src="images/ebay.png" alt="Pinball Vault Logo">
                </a>
            </div>
            <div class="title">Pinball Vault</div>
        </div>
        <div>
            <button onclick="location.href='marqueemenu.php'">Menu</button>
            <?php if ($adminMode): ?>
                <button onclick="location.href='marqueelogout.php'">Logout</button>
            <?php else: ?>
                <button onclick="location.href='marqueelogin.php'">Login</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="form-container">
            <h2>Upload Marquee</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
                
                <label for="availability">Availability:</label>
                <select name="availability" id="availability">
                    <option value="Available">Available</option>
                    <option value="Out of stock">Out of stock</option>
                </select>
                
                <label for="price">Price (GBP):</label>
                <input type="number" name="price" id="price" step="0.01" min="0" required>
                
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4"></textarea>
                
                <label for="ebayLink">eBay Link:</label>
                <input type="url" name="ebayLink" id="ebayLink" placeholder="https://www.ebay.co.uk/itm/..." required>
                
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
                
                <button type="submit">Upload Marquee</button>
            </form>
        </div>
    </div>

</body>
</html>
