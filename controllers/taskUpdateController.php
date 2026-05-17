<?php
include "../models/db.php";
include "../models/task.php";

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
            echo json_encode(array("ok" => true, "new_status" => $new_status));
        } else {
            echo json_encode(array("ok" => false));
        }
    }
}
