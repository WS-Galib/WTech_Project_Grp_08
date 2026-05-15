<?php
session_start();
include "../models/db.php";
include "../models/task.php";

$project_id = "1"; //dummy value
$database = new db();
$connection = $database->connection();
$taskDB = new task();
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
    <button onclick="document.getElementById('taskModal').style.display='block'">+ New Task</button>

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


    <div id="taskModal">
        <h2>Create New Task</h2>
        <form method="POST" action="../controllers/taskCreateController.php">
            <label for="title">Title: </label>
            <input type="text" name="title" required>
            <br><br>
            <label for="desc">Description: </label>
            <textarea name="description" placeholder="Enter the task description"></textarea>
            <br><br>
            <label for="assignedTo">Assign To: </label>
            <select name="assigned_to"> <!-- dummy value -->
                <option value="1">Student 1</option>
                <option value="2">Student 2</option>
            </select>
            <br><br>
            <label for="priority">Priority: </label>
            <input type="radio" name="priority" value="low" checked> Low
            <input type="radio" name="priority" value="medium"> Medium
            <input type="radio" name="priority" value="high"> High
            <br><br>
            <label for="due">Due Date: </label>
            <input type="date" name="due_date" required>
            <br><br>
            <input type="submit" value="Save Task">
            <button type="button" onclick="document.getElementById('taskModal').style.display='none'">Cancel</button>
        </form>
    </div>

</body>

</html>