<?php
ob_start(); // Start output buffering

session_start();
// Database connection code here

// Handle deleting a question
if (isset($_GET['delete_question'])) {
    $question_id = $_GET['delete_question'];

    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        // Redirect after successful deletion
        header("Location: dashboard.php?view=questions");
        exit(); // Make sure to call exit to stop further execution
    } else {
        echo "<p>Error deleting question: " . $conn->error . "</p>";
    }
}

ob_end_flush(); // Flush output buffer
?>
