<?php
$conn = new mysqli('localhost:3307', 'root', '', 'dbgradingsystem');

$student_id = $_POST['student_id'];
$score = $_POST['score'];

$sql = "INSERT INTO quiz_scores (student_id, score) VALUES ('$student_id', '$score')";
$conn->query($sql);
$conn->close();
?>
