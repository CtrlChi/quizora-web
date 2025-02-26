<?php
$servername = "localhost:3307";
$username = "root"; 
$password = ""; 
$dbname = "quizora"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

$username = $_POST['username'];
$password = $_POST['password'];

$response = array();

if (empty($username) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Username and password cannot be empty';
    echo json_encode($response);
    exit();
}

$sql = "SELECT user_id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $response['status'] = 'success';
        $response['user_id'] = $user['user_id']; 
        $response['username'] = $user['username'];
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Incorrect username or password';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Incorrect username or password';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
