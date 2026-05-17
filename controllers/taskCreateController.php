<?php
session_start();
include "../models/db.php";
include "../models/task.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../views/auth/login.php");
    exit();
}

$title = "";
$description = "";
$assigned_to = "";
$priority = "";
$due_date = "";
$project_id = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST["project_id"];    
    $title = $_POST["title"];
    $description = $_POST["description"];
    $assigned_to = $_POST["assigned_to"];
    $priority = $_POST["priority"];
    $due_date = $_POST["due_date"];

    if (!empty($title) && !empty($due_date) && !empty($project_id)) {
        $database = new db();
        $connection = $database->connection();
        $taskDB = new task();

        $membership = $taskDB->checkProjectMembership($connection, $project_id, $_SESSION["user_id"]);
        if (!$membership || $membership->num_rows == 0) {
            die("Access Denied: Unauthorized request scope.");
        }

        $result = $taskDB->createTask($connection, "tasks", $project_id, $title, $description, $assigned_to, $priority, $due_date);

        if ($result) {
            Header("Location: ../views/taskBoard.php?project_id=" . $project_id);
            exit();
        } else {
            $_SESSION["task_error"] = "Failed to save task to the database.";
            Header("Location: ../views/taskBoard.php?project_id=" . $project_id);
            exit();
        }
    } else {
        $_SESSION["task_error"] = "Validation Failed: Title and Due Date are required.";
        $fallback_id = !empty($project_id) ? $project_id : "1";
        Header("Location: ../views/taskBoard.php?project_id=" . $fallback_id);
        exit();
    }
}
