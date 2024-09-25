<?php
session_start();
// Include the function file
require_once "functions.php";
require_once "config.php"; // Ensure config.php is included for database connection

// Initialize variables for username and password
$username = $password = "";
$username_err = $password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            // Send Discord notification
                            $message = "<@485553416521908255> User **$username** logged in.";
                            sendDiscordNotification($message);

                            // Check if there's a redirect URL
                            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                            
                            // Prevent redirect loop by avoiding redirection to login.php
                            if ($redirect !== 'login.php' && !empty($redirect)) {
                                echo "Redirecting to: " . htmlspecialchars($redirect); // Debugging line
                                header("Location: " . htmlspecialchars($redirect));
                                exit;
                            } else {
                                // Redirect to a default page if redirect URL is login.php
                                echo "Redirecting to default: index.php"; // Debugging line
                                header("Location: index.php");
                                exit;
                            }
                        } else {
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/wallpaper.png'); /* Path to your wallpaper */
            background-size: cover; /* Ensure the image covers the whole background */
            background-repeat: no-repeat; /* Prevent repeating the image */
            background-attachment: fixed; /* Fix the image in place when scrolling */
            margin: 0;
            padding: 0;
            color: #fff; /* Default text color for better readability on the image */
        }

        .login-container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8); /* Semi-transparent dark grey */
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            align-items: center; /* Center items horizontally */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #9c27b0; /* Purple color for headings */
        }

        .form-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }

        .form-group input[type="submit"] {
            background-color: #9c27b0;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px; /* Ensure font size is appropriate */
            padding: 10px 20px; /* Ensure padding is consistent */
            width: auto; /* Adjust width to fit content */
            margin: 0; /* Remove any unintended margin */
        }

        .form-group input[type="submit"]:hover {
            background-color: #7b1fa2;
        }

        .form-group .error {
            color: red;
            font-size: 14px;
            text-align: center; /* Center the error messages */
        }

        .register-link {
            margin-top: 20px;
            text-align: center;
            width: 100%; /* Ensure the link container is full width */
        }

        .register-link a {
            color: #9c27b0;
            text-decoration: none;
            font-size: 16px;
            border: 1px solid #666;
            padding: 10px 20px;
            border-radius: 25px; /* Rounded corners */
            background-color: #333;
            display: inline-block; /* Ensure the link behaves like a button */
            width: auto; /* Adjust width to fit content */
        }

        .register-link a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
            <div class="register-link">
                <a href="register.php">Don't have an account? Register here</a>
            </div>
        </form>
    </div>
</body>
</html>
