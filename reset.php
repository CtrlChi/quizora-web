<?php
session_start();

// Only reset quiz data, do NOT destroy the session
unset($_SESSION['question_number']);
unset($_SESSION['score']);
unset($_SESSION['start_time']);
unset($_SESSION['quiz_active']);

// Redirect to the dashboard
header("Location: dashboard.php");
exit();
?>
