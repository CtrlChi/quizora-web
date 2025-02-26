<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: adminlogin.php");
    exit();
}

$conn = new mysqli('localhost:3307', 'root', '', 'quizora');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle adding a new question with AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
    $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
    $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
    $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
    $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
    $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);

    // Insert the new question into the database
    $stmt = $conn->prepare("INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);

    if ($stmt->execute()) {
        // Return a success message
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit();
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

        input[type="text"], input[type="submit"], select {
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
        input[type="text"]:focus, select:focus {
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
            display: none;
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
        <img src="trylogo3.png" alt="Logo" class="logo">
        <h2>Add Question</h2>
        <form method="POST" action="">
        <input type="hidden" name="add_question" value="1">
        <input type="text" name="question_text" placeholder="Enter question" required>
        <input type="text" name="option_a" placeholder="Option A" required>
        <input type="text" name="option_b" placeholder="Option B" required>
        <input type="text" name="option_c" placeholder="Option C" required>
        <input type="text" name="option_d" placeholder="Option D" required>
        <label for="correct_answer"><br>Select Correct Answer:</label>
        <select name="correct_answer" required>
            <option value="option_a">Option A</option>
            <option value="option_b">Option B</option>
            <option value="option_c">Option C</option>
            <option value="option_d">Option D</option>
        </select>
        <input type="submit" value="Submit">
    </form>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

    <script>
    document.querySelector("form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        let formData = new FormData(this);

        fetch("", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Question added successfully!"); // Optional success message
                this.reset(); // Clear the form
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
</script>

</body>

</html>