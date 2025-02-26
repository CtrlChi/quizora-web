<?php
session_start();
$conn = new mysqli('localhost:3307', 'root', '', 'quizora');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: adminlogin.php");
    exit();
}

$admin_username = $_SESSION['username'];
$update_message = '';

// Fetch admin details
$query = "SELECT admin_id, username FROM admin WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Check if the admin exists, if not, handle the error
if (!$admin) {
    $update_message = "Admin details not found.";
    // You can redirect or show a specific message here
    die($update_message); // Optional: stop execution if admin doesn't exist
}

// Update admin details if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['new_username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $update_message = "Passwords do not match!";
    } elseif (empty($new_username) || empty($new_password)) {
        $update_message = "Username and password cannot be empty!";
    } else {
        // Check if new username already exists
        $username_check_query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($username_check_query);
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0 && $new_username !== $admin_username) {
            $update_message = "Username is already taken.";
        } else {
            // Hash the new password securely
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT); // Secure hashing

            // Update query
            $update_query = "UPDATE admin SET username = ?, password = ? WHERE admin_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $new_username, $new_password_hash, $admin['admin_id']);

            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username; // Update session with new username
                $update_message = "Username and password updated successfully!";
            } else {
                $update_message = "Update failed. Please try again.";
            }

            $stmt->close();
        }
    }
}
?>

<!-- HTML Form here -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap">
    <style>
        body {
            font-family: 'Press Start 2P', cursive;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #0b001a, #5921a3);
            color: white;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 25px #5e37ff;
            width: 500px;
            text-align: center;
            transition: transform 0.3s;
        }
        .form-container:hover {
            transform: scale(1.03);
            box-shadow: 0 0 30px #5e37ff;
        }

        input[type="text"], input[type="password"], input[type="submit"] {
            padding: 16px;
            margin: 12px auto;
            width: 85%;
            display: block;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
            transition: 0.3s;
            text-align: center;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            box-shadow: 0 0 12px #5e37ff;
        }

        input[type="submit"] {
            background: rgb(104, 40, 189);
            color: black;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Press Start 2P', cursive;
            font-size: 1.2rem;
        }
        input[type="submit"]:hover {
            background: #7a5eff;
            box-shadow: 0 0 15px #5e37ff;
        }

        h2 {
            color: #7a5eff;
            font-size: 1.8rem;
        }

        .message {
            display: block;
            text-align: center;
            margin-top: 16px;
            padding: 16px;
            border-radius: 8px;
            font-size: 1.2rem;
            width: 100%;
        }
        .success {
            background-color: rgba(0, 255, 0, 0.3);
            border: 2px solid lime;
        }
        .error {
            background-color: rgba(255, 0, 0, 0.3);
            border: 2px solid red;
        }

        .back-btn {
            display: inline-block;
            background: #5e37ff;
            padding: 16px;
            margin-top: 20px;
            text-decoration: none;
            color: black;
            font-weight: bold;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 1.2rem;
        }
        .back-btn:hover {
            background: #7a5eff;
            box-shadow: 0 0 15px #5e37ff;
        }

        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Update Admin Credentials</h2>
        <form method="POST">
            <label>New Username:</label>
            <input type="text" name="new_username" value="<?php echo htmlspecialchars($admin['username']); ?>" required><br>
            
            <label>New Password:</label>
            <input type="password" name="new_password" required><br>
            
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>
            
            <input type="submit" value="Update">
        </form>

        <?php if (!empty($update_message)) : ?>
            <div class="message <?php echo (strpos($update_message, 'success') !== false) ? 'success' : 'error'; ?>">
                <?php echo $update_message; ?>
            </div>
        <?php endif; ?>
        
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
