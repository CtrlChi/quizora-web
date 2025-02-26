<?php
// Database connection
$servername = "localhost:3307";
$username = "root"; // your db username
$password = ""; // your db password
$dbname = "quizora"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the data from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Hash password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if the username already exists using a prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username); // "s" means the parameter is a string
$stmt->execute();
$result = $stmt->get_result();

$response = array(); // Initialize the response array

if ($result->num_rows > 0) {
    $response['status'] = 'error';
    $response['message'] = 'Username already exists';
} else {
    // Insert the new user into the database using a prepared statement
    $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $username, $hashedPassword); // "ss" means both parameters are strings
    
    if ($insert_stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'User registered successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $conn->error;
    }
}

$stmt->close();
$insert_stmt->close();
$conn->close();

// Output the response as JSON
echo json_encode($response);
?>
