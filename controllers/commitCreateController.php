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