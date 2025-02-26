<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    // If the user confirms, destroy the session and redirect to login
    session_destroy();
    header("Location: adminlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
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
            background: linear-gradient(90deg, #0b001a, #5921a3);
            background-size: 200% 100%;
            animation: gradientAnimation 5s ease infinite;
            color: white;
            overflow: hidden;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .confirmation-box {
            text-align: center;
            background: rgba(0, 0, 0, 0.85);
            padding: 40px 20px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgb(70, 19, 255), 0 0 20px rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        h2 {
            font-size: 1.5rem;
            color: white;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.7);
            letter-spacing: 3px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .button-container button,
        .button-container a {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .button-container button:hover,
        .button-container a:hover {
            background: rgb(95, 55, 207);
            border-color: rgb(95, 55, 207);
            color: white;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h2>Are you sure you want to logout?</h2>
        <form method="POST" class="button-container">
            <button type="submit" name="confirm_logout">Yes</button>
            <a href="dashboard.php">No</a>
        </form>
    </div>
</body>
</html>
