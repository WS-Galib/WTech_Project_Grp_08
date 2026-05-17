<?php
session_start();
include "../models/db.php";
include "../models/task.php";
include "../config/helpers.php";

$task_id = "";
$new_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST["task_id"];
    $new_status = $_POST["status"];

    if (!empty($task_id) && !empty($new_status)) {
        $database = new db();
        $connection = $database->connection();
        $taskDB = new task();

        $result = $taskDB->updateTaskStatus($connection, "tasks", $task_id, $new_status);

        if ($result) {
            $sql = "SELECT project_id, title FROM tasks WHERE id='" . $task_id . "'";
            $task_data = $connection->query($sql);

            if ($task_data && $task_data->num_rows > 0) {
                $row = $task_data->fetch_assoc();
                $project_id = $row["project_id"];
                $title = $row["title"];
                $user_id = $_SESSION["user_id"] ?? 1;

                $action_text = "Task '" . $title . "' moved to " . $new_status;
                log_activity($connection, $project_id, $user_id, $action_text);
            }

            echo json_encode(array("ok" => true, "new_status" => $new_status));
        } else {
            echo json_encode(array("ok" => false));
        }
    }
}
