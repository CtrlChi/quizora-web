<?php
include 'db_connection.php'; 

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $query = "SELECT user_id, username FROM users WHERE user_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_id); 
        $stmt->execute();
        $result = $stmt->get_result();

        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'User not found']);
        }

       
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Database query failed']);
    }
} else {
    echo json_encode(['error' => 'No user_id provided']);
}


$conn->close();
?>
