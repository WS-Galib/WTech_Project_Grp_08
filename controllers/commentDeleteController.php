<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["ok" => false, "error" => "Unauthenticated request."]);
    exit();
}
include "../models/db.php";
include "../config/helpers.php";
 
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $comment_id = $_GET["id"] ?? "";
    $active_user_id = $_SESSION["user_id"];

    if (empty($comment_id)) {
        echo json_encode(["ok" => false, "error" => "Invalid comment ID."]);
        exit();
    }
    $database = new db();
    $connection = $database->connection();
    $verify_sql = "SELECT c.user_id, t.title, t.project_id FROM comments c JOIN tasks t ON c.task_id = t.id WHERE c.id = ?";
    $v_stmt = $connection->prepare($verify_sql);
    $v_stmt->bind_param("i", $comment_id);
    $v_stmt->execute();
    $target_comment = $v_stmt->get_result()->fetch_assoc();
    $v_stmt->close();

    if (!$target_comment) {
        echo json_encode(["ok" => false, "error" => "Comment not found."]);
        exit();
    }

    if ($target_comment["user_id"] != $active_user_id) {
        echo json_encode(["ok" => false, "error" => "Permission denied."]);
        exit();
    }
    
