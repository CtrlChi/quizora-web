<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: adminlogin.php");
    exit();
}

$conn = new mysqli('localhost:3307', 'root', '', 'quizora');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the question to edit
if (isset($_GET['question_id'])) {
    $question_id = $_GET['question_id'];

    // Fetch question data from the database
    $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();

    if (!$question) {
        die("Question not found.");
    }
} else {
    die("No question selected.");
}

// Handle updating the question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_question'])) {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $stmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE question_id = ?");
    $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);

    if ($stmt->execute()) {
        echo "<p>Question updated successfully!</p>";
        header("Location: dashboard.php?view=questions"); 
        exit();
    } else {
        echo "<p>Error updating question: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Question</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
            background-color: rgba(0, 0, 0, 0.85);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(161, 75, 161, 0.8);
            width: 400px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .logo {
            max-width: 100px; /* Adjust the size of the logo */
            height: auto;
            margin-bottom: 20px; /* Space between logo and form */
        }
        input[type="text"], select, input[type="submit"] {
            padding: 12px;
            margin: 10px 0;
            width: 80%; 
            border-radius: 8px;
            border: 1px solid #6b3fb7;
            background-color: #1e1a2c;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input[type="submit"] {
            font-family: 'Press Start 2P', cursive; /* Add this line to use the same font */
            background-color: #6b3fb7;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4a2d8c;
        }
        select {
            background-color: #1e1a2c;
            border: 1px solid #6b3fb7;
        }
        h2 {
            color: rgb(92, 50, 219);
            text-align: center;
            margin-bottom: 20px;
        }
        .back-btn {
            background-color: #3a1d8b;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin-top: 15px;
        }
        .back-btn:hover {
            background-color: #5921a3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Logo at the top of the form -->
        <img src="trylogo3.png" alt="Logo" class="logo">
        <h2>Edit Question</h2>
        <form method="POST">
            <input type="text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" placeholder="Enter question" required>
            <input type="text" name="option_a" value="<?php echo htmlspecialchars($question['option_a']); ?>" placeholder="option_a" required>
            <input type="text" name="option_b" value="<?php echo htmlspecialchars($question['option_b']); ?>" placeholder="option_b" required>
            <input type="text" name="option_c" value="<?php echo htmlspecialchars($question['option_c']); ?>" placeholder="option_c" required>
            <input type="text" name="option_d" value="<?php echo htmlspecialchars($question['option_d']); ?>" placeholder="option_d" required>

            <select name="correct_answer" required>
                <option value="">Select Correct Answer</option>
                <option value="option_a" <?php echo $question['correct_answer'] == 'A' ? 'selected' : ''; ?>>option_a</option>
                <option value="option_b" <?php echo $question['correct_answer'] == 'B' ? 'selected' : ''; ?>>option_b</option>
                <option value="option_c" <?php echo $question['correct_answer'] == 'C' ? 'selected' : ''; ?>>option_c</option>
                <option value="option_d" <?php echo $question['correct_answer'] == 'D' ? 'selected' : ''; ?>>option_d</option>
            </select>

            <input type="submit" name="edit_question" value="Update Question">
        </form>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
    <script>
    document.querySelector("form").addEventListener("submit", function(event) {
        // Ask for confirmation
        var confirmUpdate = confirm("Are you sure you want to update this question?");
        
        if (!confirmUpdate) {
            event.preventDefault(); // If the user cancels, prevent form submission
        }
    });
</script>

</body>
</html>
