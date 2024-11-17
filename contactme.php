<?php
session_start();

// Check if admin mode is enabled
$adminMode = isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Me - Pinball Vault</title>
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
        .menu-button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .menu-button:hover {
            background-color: #666;
        }
        .content {
            padding: 20px;
            margin-top: 60px; /* To avoid overlap with the fixed header */
        }
        .contact-info {
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .contact-info img {
            max-width: 100%;
            border-radius: 5px;
        }
        .contact-info h1 {
            font-size: 24px;
            color: #ffffff;
        }
        .contact-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
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
            <button onclick="location.href='marqueemenu.php'" class="menu-button">Menu</button>
        </div>
    </div>

    <div class="content">
        <div class="contact-info">
            <img src="images/marqueecardfront.png" alt="Contact Card">
            <h1>Contact Information</h1>
            <p><strong>Name:</strong> Chris Bowers</p>
            <p><strong>Phone Number:</strong> +44 7511564331</p>
            <p><strong>Email:</strong> <a href="mailto:tranceinyapantz@gmail.com">tranceinyapantz@gmail.com</a></p>
        </div>
    </div>

</body>
</html>
