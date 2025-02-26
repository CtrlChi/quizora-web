<?php
session_start();

// Ensure quiz was completed before showing results
if (!isset($_SESSION['score']) || !isset($_SESSION['start_time']) || !isset($_SESSION['end_time'])) {
    header("Location: quizgame.php");
    exit();
}

// Calculate total time taken
$total_time = $_SESSION['end_time'] - $_SESSION['start_time'];
$minutes = floor($total_time / 60);
$seconds = $total_time % 60;

// Store results in variables
$final_score = $_SESSION['score'];
$total_questions = $_SESSION['question_number'] - 1;

// Clear session for a new game
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: linear-gradient(to right, #0b001a, #5921a3);
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .result-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 25px #5e37ff;
        }
        h1 {
            color: #ffcc00;
        }
        p {
            font-size: 1.5rem;
        }
        .btn {
            padding: 12px 24px;
            background: #ffcc00;
            color: black;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
        }
        .btn:hover {
            background: #ffd633;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h1>Quiz Completed!</h1>
        <p>Your Score: <strong><?php echo $final_score . " / " . $total_questions; ?></strong></p>
        <p>Time Taken: <strong><?php echo sprintf("%02d:%02d", $minutes, $seconds); ?></strong></p>
        <a href="quizgame.php" class="btn">Restart Quiz</a>
    </div>
</body>
</html>
