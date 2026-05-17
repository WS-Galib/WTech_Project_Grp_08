<?php
session_start();
include "../models/db.php";
include "../models/task.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../views/workspace/home.php");
    exit();
}

$project_id = $_GET["project_id"] ?? ""; 
if (empty($project_id)) {
    header("Location: ../views/workspace/home.php");
    exit();
}

$database = new db();
$connection = $database->connection();
$taskDB = new task();

$membership = $taskDB->checkProjectMembership($connection, $project_id, $_SESSION["user_id"]);
if (!$membership || $membership->num_rows == 0) {
    die("Access Denied: You are not a member of this project.");
}
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
    
    <a href="activity.php?project_id=<?php echo $project_id; ?>" style="text-decoration: none;">
        <button style="background: #10b981; margin-left: 10px;">View Activity Log</button>
    </a>

    <div class="column-container">
        <div class="column" id="todo">
            <h3>To Do</h3>
            <?php
            $todo_tasks = $taskDB->getTasksByStatus($connection, "tasks", $project_id, "todo");
            if ($todo_tasks && $todo_tasks->num_rows > 0) {
                while ($row = $todo_tasks->fetch_assoc()) {
                    $initials = "";
                    foreach (explode(" ", $row["user_name"] ?? "U N") as $w) {
                        if ($w) $initials .= strtoupper($w[0]);
                    }
                    echo "<div class='task-card' task-id='" . $row["id"] . "' due-date='" . $row["due_date"] . "'>";
                    echo "<div><b>" . $row["title"] . "</b></div>";
                    echo "<div><span class='member-initials' > " . $initials . "</span></div>";
                    echo "<div class='priority-badge priority-" . strtolower($row["priority"]) . "'>" . $row["priority"] . "</div>";
                    echo "<div><i>Due: " . $row["due_date"] . "</i></div>";
                    echo "<hr><button class='move-btn' data-direction='right'>&rarr;</button>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <div class="column" id="in-progress">
            <h3>In Progress</h3>
            <?php
            $in_progress_tasks = $taskDB->getTasksByStatus($connection, "tasks", $project_id, "in-progress");
            if ($in_progress_tasks && $in_progress_tasks->num_rows > 0) {
                while ($row = $in_progress_tasks->fetch_assoc()) {
                    $initials = "";
                    foreach (explode(" ", $row["user_name"] ?? "U N") as $w) {
                        if ($w) $initials .= strtoupper($w[0]);
                    }
                    echo "<div class='task-card' task-id='" . $row["id"] . "' due-date='" . $row["due_date"] . "'>";
                    echo "<div><b>" . $row["title"] . "</b></div>";
                    echo "<div><span class='member-initials' > " . $initials . "</span></div>";
                    echo "<div class='priority-badge priority-" . strtolower($row["priority"]) . "'>" . $row["priority"] . "</div>";
                    echo "<div><i>Due: " . $row["due_date"] . "</i></div>";
                    echo "<hr><button class='move-btn' data-direction='left'>&larr;</button> ";
                    echo "<button class='move-btn' data-direction='right'>&rarr;</button>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <div class="column" id="done">
            <h3>Done</h3>
            <?php
            $done_tasks = $taskDB->getTasksByStatus($connection, "tasks", $project_id, "done");

            if ($done_tasks && $done_tasks->num_rows > 0) {
                while ($row = $done_tasks->fetch_assoc()) {
                    $initials = "";
                    foreach (explode(" ", $row["user_name"] ?? "U N") as $w) {
                        if ($w) $initials .= strtoupper($w[0]);
                    }
                    echo "<div class='task-card' task-id='" . $row["id"] . "' due-date='" . $row["due_date"] . "'>";
                    echo "<div><b>" . $row["title"] . "</b></div>";
                    echo "<div><span class='member-initials' > " . $initials . "</span></div>";
                    echo "<div class='priority-badge priority-" . strtolower($row["priority"]) . "'>" . $row["priority"] . "</div>";
                    echo "<div><i>Due: " . $row["due_date"] . "</i></div>";
                    echo "<hr><button class='move-btn' data-direction='left'>&larr;</button> ";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>

    <div id="taskModal" style="display: <?php echo isset($_SESSION['task_error']) ? 'block' : 'none'; ?>;">
        <h2>Create New Task</h2>

        <?php
        if (isset($_SESSION["task_error"])) {
            echo "<div class='error-msg'><b>Error:</b> " . $_SESSION["task_error"] . "</div>";
            unset($_SESSION["task_error"]);
        }
        ?>

        <form method="POST" action="../controllers/taskCreateController.php">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <label for="title">Title: </label>
            <input type="text" name="title" required>
            <br><br>
            <label for="desc">Description: </label>
            <textarea name="description" placeholder="Enter the task description"></textarea>
            <br><br>
            <label for="assignedTo">Assign To: </label>
            <select name="assigned_to">
                <?php
                $members = $taskDB->getProjectMembers($connection, $project_id);
                if ($members && $members->num_rows > 0) {
                    while ($member_row = $members->fetch_assoc()) {
                        echo "<option value='" . $member_row["user_id"] . "'>" . $member_row["name"] . "</option>";
                    }
                }
                ?>
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

    <script src="../assets/js/taskUpdate.js"></script>
    <script src="../assets/js/comments.js"></script>
</body>

</html>