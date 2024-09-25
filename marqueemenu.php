<?php
session_start();

// Check if admin mode is enabled
$adminMode = isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'];

// Directory containing marquee directories
$marqueeDir = 'Marquee/';
$marquees = [];

// Read the directories
if (is_dir($marqueeDir)) {
    $directories = array_filter(glob($marqueeDir . '*'), 'is_dir');
    foreach ($directories as $directory) {
        $title = basename($directory);
        $thumbnail = $directory . '/thumbnail.png'; // Adjusted to '.png'
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
            'thumbnail' => file_exists($thumbnail) ? $thumbnail : 'images/marqueedefaultplaceholder.png',
            'description' => isset($info['Description']) ? $info['Description'] : '',
            'ebay_link' => isset($info['Ebay Link']) ? $info['Ebay Link'] : '',
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
    <title>Marquee Menu - Pinball Vault</title>
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
        .upload-button, .edit-button, .login-button, .contact-button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 5px;
        }
        .upload-button:hover, .edit-button:hover, .login-button:hover, .contact-button:hover {
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
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-title">
            <div class="logo">
                <a href="https://www.ebay.co.uk/usr/tranceinyapantz" target="_blank" rel="noopener noreferrer">
                    <img src="images/ebay.png" alt="Ebay">
                </a>
            </div>
            <div class="title">Pinball Vault</div>
        </div>
        <div>
            <button onclick="location.href='marqueecontactme.php'" class="contact-button">Contact Me</button>
            <?php if ($adminMode): ?>
                <button onclick="location.href='marqueeupload.php'" class="upload-button">Upload</button>
                <button onclick="location.href='marqueeedit.php'" class="edit-button">Edit</button>
                <button onclick="location.href='marqueelogout.php'">Logout</button>
            <?php else: ?>
                <button onclick="location.href='marqueelogin.php'" class="login-button">Login</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="marquee-grid">
            <?php if (empty($marquees)): ?>
                <div class="marquee-item">
                    <p>No marquees found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($marquees as $marquee): ?>
                    <div class="marquee-item">
                        <a href="<?= htmlspecialchars($marquee['thumbnail']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($marquee['thumbnail']) ?>" alt="<?= htmlspecialchars($marquee['title']) ?>">
                        </a>
                        <div class="info">
                            <a href="<?= htmlspecialchars($marquee['ebay_link']) ?>" class="title" target="_blank" rel="noopener noreferrer">
                                <?= htmlspecialchars($marquee['title']) ?>
                            </a>
                            <div class="description"><?= htmlspecialchars($marquee['description']) ?></div>
                            <div class="<?= htmlspecialchars($marquee['availability'] == 'Available' ? 'available' : 'out-of-stock') ?>">
                                <?= htmlspecialchars($marquee['availability']) ?>
                            </div>
                            <div>Price: £<?= htmlspecialchars($marquee['price']) ?></div>
                        </div>
                        <?php if ($adminMode): ?>
                            <form method="post" action="marqueedelete.php" style="position: absolute; top: 10px; right: 10px;">
                                <input type="hidden" name="title" value="<?= htmlspecialchars($marquee['title']) ?>">
                                <button type="submit" style="background-color: #f44336; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
