<?php
session_start();

$adminUsername = "tranceinyapantz";
$adminPassword = "thisistheadminpassword";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['admin_mode'] = true;
        header("Location: marqueemenu.php");
        exit();
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pinball Vault</title>
    <style>
        body {
            background-color: #2e2e2e;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px;
            position: absolute;
            top: 0;
            left: 0;
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
        .header button {
            background-color: #444;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .header button:hover {
            background-color: #555;
        }
        .login-container {
            text-align: center;
            padding: 20px;
            background-color: #444;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            width: 300px;
            box-sizing: border-box;
        }
        .login-container input {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #555;
            background-color: #333;
            color: #e0e0e0;
            text-align: center;
            box-sizing: border-box;
        }
        .login-container button {
            background-color: #555;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .login-container button:hover {
            background-color: #666;
        }
        .error {
            color: #f44336;
            margin-top: 10px;
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
            <button onclick="location.href='marqueelogin.php'">Login</button>
        </div>
    </div>

    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($loginError)): ?>
            <div class="error"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>
    </div>

</body>
</html>
