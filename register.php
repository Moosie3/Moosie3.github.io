<?php
session_start();
require_once 'config.php';

// Handle form submission
if (isset($_POST['register'])) {
    // Collect and sanitize input
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = mysqli_real_escape_string($link, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($link, $_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($link, $sql);

        if ($result === false) {
            // Output SQL error for debugging
            $error = "Error executing query: " . mysqli_error($link);
        } elseif (mysqli_num_rows($result) > 0) {
            $error = "Username already exists.";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if (mysqli_query($link, $sql)) {
                $success = "Account created successfully. <a href='login.php'>Login</a>";
                
                // Send webhook notification
                $webhook_url = 'https://discord.com/api/webhooks/1216835175019708526/fO-gKCvZgmIeIYEhgTcQFYKKFMMJAiEXlnCY97FygmzNfTdHPPoUGybEpMXm_oNZ-6Sm';
                $message = [
                    "content" => "<@485553416521908255> New account created: **$username**",
                ];
                
                $ch = curl_init($webhook_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                
                if ($response === false) {
                    $error = 'Webhook error: ' . curl_error($ch);
                }
                
                curl_close($ch);
            } else {
                // Output SQL error for debugging
                $error = "Error executing query: " . mysqli_error($link);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-image: url('images/wallpaper.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .header {
            position: fixed;
            top: 0;
            right: 0;
            padding: 10px;
            background-color: rgba(68, 68, 68, 0.8);
            border-bottom: 1px solid #666;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .header a {
            color: #9c27b0;
            text-decoration: none;
            font-size: 16px;
            margin-left: 20px;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .main-form {
            max-width: 600px;
            margin: 100px auto 20px; /* Added top margin to account for fixed header */
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8);
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            text-align: center;
        }

        .main-form h1 {
            color: #9c27b0;
        }

        .main-form input {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #666;
            background-color: #333;
            color: #fff;
        }

        .main-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #9c27b0;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .main-form button:hover {
            background-color: #7b1fa2;
        }

        .main-form p {
            color: #fff;
        }

        .main-form a {
            color: #9c27b0;
            text-decoration: none;
        }

        .main-form a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="login.php">Login</a>
        <a href="register.php">Create Account</a>
    </div>

    <div class="main-form">
        <h1>Create Account</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($success)): ?>
            <p><?php echo $success; ?></p>
        <?php elseif (!empty($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
