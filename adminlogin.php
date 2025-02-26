<?php
session_start();
$conn = new mysqli('localhost:3307', 'root', '', 'quizora');

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($username == 'aya' && $password == 'aaa') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = 'aya';
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = 'Invalid username or password!';
    }

    if ($username == 'chi' && $password == 'aaa') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = 'chi';
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = 'Invalid username or password!';
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap">
    <style>
    body {
        font-family: 'Press Start 2P', cursive; /* Apply the Press Start font to the whole body */
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
        flex-direction: column; /* Stack the logo and login container vertically */
    }

    @keyframes gradientAnimation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }


    /* Logo styling */
    .logo-container {
        position: absolute;
        top: 20px; /* Adjust the position of the logo */
        left: 50%;
        transform: translateX(-50%);
        z-index: 1; /* Ensure the logo appears above other elements */
    }

    .logo-container img {
        max-width: 180px; /* Set a maximum size */
        height: auto;
    }

    /* Login container animation */
    @keyframes formAnimation {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-container {
        background: rgba(0, 0, 0, 0.85);
        padding: 40px 20px;
        border-radius: 15px;
        box-shadow: 0 0 30px rgb(70, 19, 255), 0 0 20px rgba(255, 255, 255, 0.3);
        text-align: center;
        width: 100%;
        max-width: 600px;
        min-height: 350px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        animation: formAnimation 1s ease-out; /* Add form animation */
    }

    h2 {
        font-size: 2rem;
        color: white;
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.7);
        letter-spacing: 3px;
        margin-bottom: 50px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px;
    }

    label {
        font-size: 1rem;
        letter-spacing: 1px;
        color: white;
    }

    input[type="text"], input[type="password"] {
        width: 80%;
        padding: 10px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 1rem;
    }

    /* Login Button Styling */
    button {
        background: linear-gradient(45deg, #9b27f0, #3a1d8b);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 15px;
        font-family: 'Press Start 2P', cursive; /* Apply the Press Start 2P font to the button */
        font-size: 1.2rem;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
        margin-bottom: 30px;

    }

    /* Button hover animation */
    button:hover {
        background: linear-gradient(45deg, #4c2ccf, #7228b7);
        transform: scale(1.1); /* Scale up the button on hover */
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5), 0 4px 30px rgba(92, 50, 219, 0.7);
    }

    button:active {
        transform: scale(1); /* Reset scale on active */
    }

    a {
        color: #3a1d8b;
        text-decoration: none;
        margin-top: 10px;
    }

    a:hover {
        color: rgb(95, 55, 207);
    }

    .error-message {
        color: red;
        font-size: 1.2rem;
        margin-top: 10px;
    }

    </style>
</head>
<body>
    <!-- Logo outside the login card -->
    <div class="logo-container">
        <img src="trylogo3.png" alt="Quizora Logo">
    </div>

    <div class="login-container">
        <h2>QUIZORA ADMIN</h2>
        <form method="POST" action="adminlogin.php">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>

        <?php if ($error_message != ''): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
