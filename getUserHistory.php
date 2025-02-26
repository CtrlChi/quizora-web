<?php
    // Example database connection
    include('db_connection.php');

    // Get the user_id from the GET request
    $user_id = $_GET['user_id'];

    // Prepare the query to fetch time_taken, score, and created_at
    $query = "SELECT time_taken, score, created_at FROM user_scores WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store the history
    $history = [];

    while ($row = $result->fetch_assoc()) {
        $time_taken = $row['time_taken'];
        $score = $row['score'];
        $created_at = $row['created_at'];

        // Convert time_taken from seconds to minutes and seconds
        $minutes = floor($time_taken / 60);
        $seconds = $time_taken % 60;

        // Format the time as "X minute(s) Y second(s)"
        $formatted_time = "$minutes minute" . ($minutes > 1 ? "s" : "") . " $seconds second" . ($seconds != 1 ? "s" : "");

        // Add the data to the history array
        $history[] = [
            'time_taken' => $formatted_time,
            'score' => $score,
            'created_at' => $created_at
        ];
    }

    // Send the JSON response
    echo json_encode(['status' => 'success', 'history' => $history]);
?>
