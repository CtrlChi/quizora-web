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

// Fetch registered users
$result_users = $conn->query("SELECT user_id, username FROM users");
if (!$result_users) {
    die("Error fetching users: " . $conn->error);
}

// Fetch quiz questions
$result_questions = $conn->query("SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_answer FROM quiz_questions");
if (!$result_questions) {
    die("Error fetching quiz questions: " . $conn->error);
}

// Handle adding new questions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $stmt = $conn->prepare("INSERT INTO quiz_questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Question added successfully!';
    } else {
        $_SESSION['message'] = 'Error adding question: ' . $conn->error;
    }
    header("Location: dashboard.php"); 
    exit();
}

// Handle editing a question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_question'])) {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $stmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE question_id = ?");
    $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Question updated successfully!';
    } else {
        $_SESSION['message'] = 'Error updating question: ' . $conn->error;
    }
    header("Location: dashboard.php"); // Redirect to refresh page and show the message
    exit();
}

// Handle deleting a question
if (isset($_GET['delete_question'])) {
    $question_id = $_GET['delete_question'];

    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Question deleted successfully!';
    } else {
        $_SESSION['message'] = 'Error deleting question: ' . $conn->error;
    }
    header("Location: dashboard.php"); // Redirect to refresh page and show the message
    exit();
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap">
    <style>
    body {
        font-family: 'Press Start 2P', cursive;
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: #000;
        color: white;
        overflow: hidden; /* Ensure no scrolling of the body */
    }
    header {
        background: linear-gradient(to right, #0b001a, #5921a3);
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }    

    .logo-container img {
        height: 100px; /* Set the desired height */
        width: auto; /* Let the width adjust automatically */
        margin-bottom: 10px;
    }

    .admin-info {
        color: white;
        font-size: 1rem; 
        margin-top: 5px;
        text-shadow: 0 0 5px #a855f7, 0 0 10px #9333ea, 0 0 15px #7e22ce;
    }

    .admin-info,
    .links-container {
        width: 100%;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3); /* Same color as the border */
        padding-bottom: 10px; /* Space between logo and line */
    }

    .links-container {
        margin-top: 5px; /* Space above the links container */
        text-align: center;
        display: flex; /* Use flexbox for alignment */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        gap: 10px; /* Reduced spacing between the links */
        height: 50px; /* Reduced container height */
    }

    .links-container a {
        color: rgba(255, 255, 255, 0.3); /* Matches the border color */
        font-size: 1rem; /* Smaller font size */
        text-decoration: none;
        padding: 5px 15px; /* Smaller padding for a compact look */
        border: 2px solid rgba(255, 255, 255, 0.3); /* Matches the line color */
        border-radius: 8px; /* Slightly smaller rounded corners */
        display: inline-block; /* Ensures proper alignment */
        line-height: normal; /* Reset line height for consistent centering */
    }

    .links-container a:hover {
        color: rgb(95, 55, 207); /* Highlighted text color */
        border-color: rgb(95, 55, 207); /* Change border color on hover */
        background: rgba(255, 255, 255, 0.1); /* Optional: subtle hover background */
    }

    .dashboard-content {
        flex: 1; /* Take up all available space below the header */
        width: 100%; /* Expand to the full width of the page */
        background: #000; /* Pure black background */
        box-shadow: 0 0 30px rgb(70, 19, 255), 0 0 20px rgba(255, 255, 255, 0.3); /* Glowing sides */
        text-align: center;
        overflow-y: auto; /* Allow scrolling for overflowing content */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        height: calc(100vh - 100px); /* Adjust height based on the header's height */
        margin: 0px;
    }

    .dashboard-content table-container {
        width: 100%;
        height: 100%;
        overflow-x: auto; /* Allow horizontal scrolling for wide tables */
        overflow-y: auto; /* Allow vertical scrolling for long tables */
    }
    table {
        width: 90%;
        border-collapse: collapse;
        table-layout: auto;
    }
    table, th, td {
        border: 0.5px solid white;
    }

    th, td {
        padding: 1rem;
        font-size: 1rem;
        word-wrap: break-word;
    }
    th {
        background-color: rgb(92, 50, 219);
    }

    td {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .form-container input[type="text"], .form-container select, .form-container input[type="submit"] {
        font-size: 0.85rem; /* Smaller font size for form inputs */
        padding: 10px; /* Slightly smaller padding */
    }

    .form-container input[type="submit"] {
        font-size: 0.85rem; /* Smaller font size for the submit button */
    }

    .form-container h2 {
        font-size: 1.25rem; /* Slightly smaller heading font size */
        color: rgb(92, 50, 219);
    }
    @media (max-width: 768px) {
        th, td {
            font-size: 0.8rem; /* Reduce font size */
            padding: 0.5rem; /* Reduce padding */
        }

        table {
            font-size: 0.8rem; /* Reduce overall table font size */
        }
    }    

    .home-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin: 30px auto;
        padding: 2rem;
        width: 90%;
        max-width: 1200px;
        background: linear-gradient(145deg, #0b001a, #5921a3);
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.4);
        opacity: 0; /* Initially hidden for animation */
        animation: fadeIn 1s forwards; /* Fade-in animation */
    }

    @keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

    .home-container h1 {
        font-size: 2.8rem;
        margin-bottom: 1.2rem;
        color: white;
        text-shadow: 0px 0px 10px rgba(255, 255, 255, 0.7);
        animation: typing 3s steps(20) 1s forwards, blink 0.75s step-end infinite; /* Typing and blinking cursor effect */
    }

    @keyframes typing {
    from {
            width: 0;
        }
        to {
            width: 20ch; /* Adjust for text length */
        }
    }

    @keyframes blink {
        50% {
            border-color: transparent;
        }
    }

/* Intro Text Fade-In Animation */
    .home-container .intro {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.8);
        opacity: 0;
        animation: fadeInText 1s ease-in-out 2s forwards; /* Fade-in after title */
    }
    @keyframes fadeInText {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }
    /* Button Styles */
    .home-container .button {
        margin-top: 20px;
        padding: 15px 30px;
        background: #5e37ff;
        color: white;
        font-size: 1.2rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        opacity: 0;
        animation: fadeInButton 1s ease-in-out 3s forwards; /* Fade-in Button */
    }

    @keyframes fadeInButton {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.4);
    }

    .home-container .feature-card a {
        display: inline-block;
        margin-top: 1rem; /* Space above the buttons */
        padding: 10px 20px; /* Uniform padding for all buttons */
        background: #5e37ff;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        font-size: 1rem; /* Same font size for all buttons */
        text-align: center;
        width: 100%; /* Make buttons take full width of the container */
        max-width: 250px; /* Set a maximum width for uniformity */
        box-sizing: border-box; /* Ensure padding is included in the width calculation */
        transition: background 0.3s ease;
    }

    .home-container .feature-card a:hover {
        background: #805dff;
    }


    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #d1c4ff;
    }

    .feature-card p {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .feature-card a {
        display: inline-block;
        margin-top: 0.5rem;
        padding: 0.5rem 1rem;
        background: #5e37ff;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background 0.3s ease;
    }

    .feature-card a:hover {
        background: #805dff;
    }
    
    .home-intro-container {
        background: linear-gradient(145deg, #0b001a, #5921a3); /* Gradient background */
        color: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.4);
        text-align: center;
        max-width: 900px;
        margin: 30px auto;
        opacity: 0;
        animation: fadeIn 1s ease-in-out forwards; /* Fade-in animation */
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .home-intro-container h2 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #d1c4ff;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
    }

    .home-description {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.6;
    }

    .home-description:hover {
        color: #e5e5e5; /* Slight hover effect */
    }
    .quizora-cta {
        font-size: 2rem; /* Larger text for emphasis */
        font-weight: bold; /* Bold text */
        color:rgb(255, 255, 255); /* Color matching your theme */
        margin-top: 40px; /* Adds some space above */
        text-align: center; /* Centers the text */
        text-transform: uppercase; /* Uppercase letters for impact */
        letter-spacing: 3px; /* Spaced out letters for a more dynamic look */
        animation: fadeInText 1s ease-in-out forwards, bounceIn 1s ease-in-out 0.5s forwards; /* Combined animations */
    }

    /* Animation for fade-in effect */
    @keyframes fadeInText {
        0% {
            opacity: 0;
            transform: translateY(20px); /* Starts off screen */
        }
        100% {
            opacity: 1;
            transform: translateY(0); /* Ends at original position */
        }
    }

    /* Button styling */
    .dashboard-btn {
        display: inline-block;
        background-color: #5921a3; /* Same as the gradient's secondary color */
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        text-decoration: none;
        border-radius: 25px;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
        margin-top: 20px;
    }

    /* Button hover effect */
    .dashboard-btn:hover {
        background-color: #7a3ec5;  /* Lighter purple on hover */
        transform: translateY(-3px); /* Slightly lift the button on hover */
    }




    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="trylogo31.png" alt="Quizora Logo">
        </div>
        <div class="admin-info">
        <span>ADMIN <?php echo $_SESSION['username']; ?></span>
        </div>
        <div class="links-container">
        <a href="?view=home">Home</a>
        <a href="quizgame.php">Try Quiz</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
        
    </div>
    </header>
    
    <div class="dashboard-content">

    <?php
        // Updated content for the home view
    if (!isset($_GET['view'])) {
        echo "<div class='home-container'>";
        echo "<h1>Welcome to Quizora Admin Dashboard!</h1>";
        echo "<p class='intro'>Effortlessly manage your quiz platform from one central location.</p>";
        echo "<div class='features-grid'>";
        echo "<div class='feature-card'>";
        echo "<h3>💻 <br><br> Manage Players</h3>";
        echo "<p>View, monitor, and manage registered players on the platform.</p>";
        echo "<a href='?view=users'>View Players</a>";
        echo "</div>";
        echo "<div class='feature-card'>";
        echo "<h3>❓ <br><br> Manage Questions </h3>";
        echo "<p>Edit, add, or delete quiz questions with ease.</p>";
        echo "<a href='?view=questions'>Manage Questions</a>";
        echo "</div>";
        echo "<div class='feature-card'>";
        echo "<h3>✨ <br><br> Add Questions</h3>";
        echo "<p>Add fresh and challenging questions to keep your quiz exciting.</p>";
        echo "<a href='add_question.php'>Add Question</a>";
        echo "</div>";
        echo "<div class='feature-card'>";
        echo "<h3>🚪 <br><br><br> Logout</h3>";
        echo "<p>Securely log out of the admin dashboard when done.</p>";
        echo "<a href='logout.php'>Logout</a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        
    } elseif ($_GET['view'] == 'home') {
        echo '<div class="home-intro-container">';
        echo '<h2>About Quizora</h2>';
        echo '<p class="home-description">Step into the world of fun and fast-paced quizzes designed to test, refresh, and enhance your general knowledge. </p>';
        echo '<p class="home-description">With a stopwatch ticking for the whole quiz, challenge yourself to think quickly and stay sharp across various topics.</p>';
        echo '<a href="add_question.php" class="dashboard-btn">Start Adding Questions</a>';
        echo '</div>';
        
        echo '<div class="home-intro-container">';
        echo '<h2>Quizora Admin</h2>';
        echo '<p class="home-description">This platform is designed to give you complete control over the quiz game. Manage user registrations, edit and add quiz questions, and keep the game running smoothly.</p>';
        echo '<a href="dashboard.php" class="dashboard-btn">Go to Dashboard</a>';  // Button to dashboard
        echo '</div>';
        
        // Added call to action text with animation
        echo '<p class="quizora-cta">Think quick. Answer quicker. Quizora awaits!</p>';
        
        
    } elseif ($_GET['view'] == 'users') {
        // Display registered users
        echo "<h1><br>Registered Players</h1>";
        echo "<table>";
        echo "<tr><th>User ID</th><th>Username</th></tr>";
        while ($row = $result_users->fetch_assoc()) {
            echo "<tr><td>" . $row['user_id'] . "</td><td>" . $row['username'] . "</td></tr>";
        }
        echo "</table>";
        echo '</div>';
        echo '<a href="dashboard.php" class="dashboard-btn">Back to Dashboard</a>';  // Button to dashboard
        echo '<br';


    } elseif ($_GET['view'] == 'questions') {
        // Display quiz questions
        echo "<h1><br>Quiz Questions</h1>";
        echo "<table>";
        echo "<tr><th>Quiz ID</th><th>Question</th><th>Option A</th><th>Option B</th><th>Option C</th><th>Option D</th><th>Correct Answer</th><th>Actions</th></tr>";
        while ($row = $result_questions->fetch_assoc()) {
            echo "<tr><td>" . $row['question_id'] . "</td><td>" . $row['question_text'] . "</td><td>" . $row['option_a'] . "</td><td>" . $row['option_b'] . "</td><td>" . $row['option_c'] . "</td><td>" . $row['option_d'] . "</td><td>" . $row['correct_answer'] . "</td>";
            echo "<td><a href='edit_question.php?question_id=" . $row['question_id'] . "'>Edit</a> | <a href='?delete_question=" . $row['question_id'] . "' onclick='return confirm(\"Are you sure you want to delete this question?\")'>Delete</a></td></tr>";
        }
        echo "</table>";
        echo '</div>';
        echo '<a href="dashboard.php" class="dashboard-btn">Back to Dashboard</a>';  // Button to dashboard
        echo '<br';
    }
        
    ?>

    </div>
</body>
</html>



