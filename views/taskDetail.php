<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../views/login.php");
    exit();
}
$task_id = $_GET["task_id"] ?? "";
if (empty($task_id)) {
    header("Location: ../views/dashboard.php");
    exit();
}
include "../models/db.php";
$database = new db();
$connection = $database->connection();
$task_query = "SELECT t.*, p.id AS project_id FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.id = ?";
$stmt = $connection->prepare($task_query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task_result = $stmt->get_result();
$task = $task_result->fetch_assoc();

if (!$task) {
    die("Task not found.");
}
$comments_query = "SELECT c.*, u.name AS author_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.task_id = ? ORDER BY c.created_at ASC";
$c_stmt = $connection->prepare($comments_query);
$c_stmt->bind_param("i", $task_id);
$c_stmt->execute();
$comments_result = $c_stmt->get_result();
?>
