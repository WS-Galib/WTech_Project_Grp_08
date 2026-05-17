<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["ok" => false, "error" => "Unauthenticated request."]);
    exit();
}

include "../models/db.php";
include "../config/helpers.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $task_id = $_POST["task_id"] ?? "";
    $project_id = $_POST["project_id"] ?? "";
    $body = trim($_POST["body"] ?? "");
    $user_id = $_SESSION["user_id"];
    $author_name = $_SESSION["name"] ?? "Team Member"; 

    if (empty($task_id) || empty($project_id) || empty($body)) {
        echo json_encode(["ok" => false, "error" => "Validation Failed: Comment text cannot be empty."]);
        exit();
    }
    $database = new db();
    $connection = $database->connection();

    $title_query = "SELECT title FROM tasks WHERE id = ?";
    $t_stmt = $connection->prepare($title_query);
    $t_stmt->bind_param("i", $task_id);
    $t_stmt->execute();
    $title_result = $t_stmt->get_result()->fetch_assoc();
    $task_title = $title_result ? $title_result["title"] : "Unknown Task";
    $t_stmt->close();

    $insert_sql = "INSERT INTO comments (task_id, user_id, body, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $connection->prepare($insert_sql);
    
    if ($stmt) {
        $stmt->bind_param("iis", $task_id, $user_id, $body);
        
        if ($stmt->execute()) {
            $new_comment_id = $stmt->insert_id;
            $stmt->close();
            $action_text = "Commented on task '" . $task_title . "'";
            log_activity($connection, $project_id, $user_id, $action_text);
            
