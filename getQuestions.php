<?php
$servername = "localhost:3307";  // Database server name or IP address
$username = "root";         // Database username (replace with your username)
$password = "";             // Database password (replace with your password)
$dbname = "quizora";        // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch quiz questions
$sql = "SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_answer FROM quiz_questions";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    // Output any SQL error
    die("Error executing query: " . $conn->error);
}

$questions = array();

// Fetch data and store in array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    echo json_encode(["message" => "No questions found"]);
}

$conn->close();

// Return questions as JSON
echo json_encode($questions);
?>
