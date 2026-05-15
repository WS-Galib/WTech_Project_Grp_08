<?php
session_start();
include "../models/db.php";

$project_id = "1"; //dummy value
$database = new db();
$connection = $database->connection();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Task Board</title>
    <link rel="stylesheet" href="../assets/css/taskBoard.css">
</head>

<body>
    <h1>Task Board</h1>
    <hr>
    <button>+ New Task</button>
    <div class="column-container">
        <div class="column" id="todo">
            <h3>To Do</h3>
        </div>
        <div class="column" id="in-progress">
            <h3>In Progress</h3>
        </div>
        <div class="column" id="done">
            <h3>Done</h3>
        </div>
    </div>
</body>

</html>