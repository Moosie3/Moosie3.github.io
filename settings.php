<?php
session_start();

// Check if the user is logged in and has the correct permissions
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["username"] !== "Local_Floof") {
    header("Location: index.php");
    exit;
}

require_once "config.php"; // Database connection file

// Initialize error and success messages
$update_err = $delete_err = $success_msg = "";

// Handle password update
if (isset($_POST['update_password'])) {
    $user_id = $_POST['user_id'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $update_err = "Please fill in both password fields.";
    } elseif ($new_password !== $confirm_password) {
        $update_err = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Password updated successfully.";
            } else {
                $update_err = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle account deletion
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Account deleted successfully.";
        } else {
            $delete_err = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle access permission update
if (isset($_POST['update_permission'])) {
    $user_id = $_POST['user_id'];
    $can_access = isset($_POST['can_access']) ? 1 : 0;

    $sql = "UPDATE users SET can_access_file_share = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $can_access, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "User permissions updated successfully.";
        } else {
            $update_err = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all users
$sql = "SELECT id, username, can_access_file_share FROM users";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
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

        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(68, 68, 68, 0.8); /* Semi-transparent dark grey */
            border: 1px solid #666;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #9c27b0; /* Purple color for headings */
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table th, .user-table td {
            padding: 10px;
            border: 1px solid #666;
            text-align: center;
        }

        .user-table th {
            background-color: #333;
        }

        .user-table td {
            background-color: #444;
        }

        .form-group {
            margin-bottom: 15px;
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
        }

        .form-group input[type="submit"]:hover {
            background-color: #7b1fa2;
        }

        .message {
            color: #9c27b0;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

        .action-links a {
            color: #9c27b0;
            text-decoration: none;
        }

        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        <?php if (!empty($success_msg)): ?>
            <div class="message"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if (!empty($update_err)): ?>
            <div class="error"><?php echo $update_err; ?></div>
        <?php endif; ?>
        <?php if (!empty($delete_err)): ?>
            <div class="error"><?php echo $delete_err; ?></div>
        <?php endif; ?>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Access to File Share</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <form action="settings.php" method="post">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <input type="checkbox" name="can_access" <?php echo $row['can_access_file_share'] ? 'checked' : ''; ?> 
                                    onchange="this.form.submit();">
                                <input type="hidden" name="update_permission" value="1"> <!-- Hidden input to indicate permission update -->
                            </form>
                        </td>
                        <td class="action-links">
                            <a href="#" onclick="document.getElementById('update-form-<?php echo $row['id']; ?>').style.display='block'">Change Password</a> |
                            <a href="settings.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this account?')">Delete Account</a>
                        </td>
                    </tr>
                    <tr id="update-form-<?php echo $row['id']; ?>" style="display: none;">
                        <td colspan="3">
                            <form action="settings.php" method="post">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password">
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="update_password" value="Update Password">
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($link);
?>
