<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "quizora");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => $conn->connect_error]));
}

// Get POST data
$user_id = $_POST['user_id'];
$score = $_POST['score'];
$time_taken = $_POST['time_taken']; // Time in seconds

if (empty($user_id) || empty($score) || empty($time_taken)) {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
    exit();
}

// Insert into the scores table
$sql = "INSERT INTO user_scores (user_id, score, time_taken) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $score, $time_taken);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Score saved successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
