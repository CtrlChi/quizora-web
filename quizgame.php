<?php
session_start();

// If the page is refreshed or exited, reset the session
if (!isset($_SESSION['quiz_active'])) {
    // Do NOT destroy session, just reset quiz-related variables
    $_SESSION['question_number'] = 1;
    $_SESSION['score'] = 0;
    $_SESSION['start_time'] = time();
    $_SESSION['quiz_active'] = true;
}

$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "quizora";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the current question
$question_number = $_SESSION['question_number'];
$query = "SELECT * FROM quiz_questions WHERE question_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question_number);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();
$stmt->close();

// If no more questions, store end time and redirect to results page
if (!$question) {
    $_SESSION['end_time'] = time();
    header("Location: result.php");
    exit();
}

// Handle answer submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_answer = $_POST['answer'];
    if ($selected_answer == $question['correct_answer']) {
        $_SESSION['score']++;
    }
    $_SESSION['question_number']++;
    header("Location: quizgame.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Game</title>
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

        .quiz-container {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 25px #5e37ff;
            width: 500px;
            text-align: center;
            transition: transform 0.3s;
        }
        .quiz-container:hover {
            transform: scale(1.03);
            box-shadow: 0 0 30px #5e37ff;
        }

        h2 {
            color: #7a5eff;
            font-size: 1.8rem;
        }

        #timer {
            font-size: 1.5rem;
            color: #ffcc00;
            margin-bottom: 20px;
        }

        button {
            padding: 16px;
            margin: 12px auto;
            width: 85%;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }
        button:hover {
            background: #7a5eff;
            box-shadow: 0 0 15px #5e37ff;
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

    </style>
</head>
<body>
    <div class="quiz-container">
        <h2>Question <?php echo $question_number; ?></h2>
        <div id="timer">Time: 00:00</div>
        <p><?php echo htmlspecialchars($question['question_text']); ?></p>
        <form method="POST">
            <button type="submit" name="answer" value="<?php echo htmlspecialchars($question['option_a']); ?>"><?php echo htmlspecialchars($question['option_a']); ?></button>
            <button type="submit" name="answer" value="<?php echo htmlspecialchars($question['option_b']); ?>"><?php echo htmlspecialchars($question['option_b']); ?></button>
            <button type="submit" name="answer" value="<?php echo htmlspecialchars($question['option_c']); ?>"><?php echo htmlspecialchars($question['option_c']); ?></button>
            <button type="submit" name="answer" value="<?php echo htmlspecialchars($question['option_d']); ?>"><?php echo htmlspecialchars($question['option_d']); ?></button>
        </form>
        <a href="dashboard.php" class="back-btn">Back</a>
    </div>

    <script>
        let startTime = <?php echo $_SESSION['start_time']; ?>;
        
        function updateTimer() {
            let elapsedTime = Math.floor(Date.now() / 1000) - startTime;
            let minutes = Math.floor(elapsedTime / 60);
            let seconds = elapsedTime % 60;
            document.getElementById("timer").textContent = `Time: ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);


        document.addEventListener("visibilitychange", function () {
        if (document.hidden) {
            // User switched tabs, reset the session
            fetch("reset.php").then(() => {
                window.location.href = "dashboard.php"; // Redirect to start page
            });
        }

        });

    </script>
</body>
</html>
