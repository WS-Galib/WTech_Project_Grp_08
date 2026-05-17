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
<!DOCTYPE html>
<html>
<head>
    <title>Task Details - <?php echo htmlspecialchars($task['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/taskBoard.css">
    <style>
        .detail-container { max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 14px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .back-link { display: inline-block; margin-bottom: 20px; color: #4f46e5; text-decoration: none; font-weight: 600; }
        .comment-box { border-bottom: 1px solid #e5e7eb; padding: 12px 0; position: relative; }
        .comment-meta { font-size: 12px; color: #64748b; margin-bottom: 4px; }
        .comment-body { font-size: 14px; color: #334155; }
        .delete-link { color: #ef4444; font-size: 12px; text-decoration: none; position: absolute; right: 0; top: 12px; cursor: pointer; }
        .comment-form { margin-top: 25px; }
        .comment-form textarea { width: 100%; height: 90px; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; resize: none; outline: none; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="detail-container">
        <a href="taskBoard.php?project_id=<?php echo $task['project_id']; ?>" class="back-link">&larr; Back to Task Board</a>
        
        <h2><?php echo htmlspecialchars($task['title']); ?></h2>
        <p style="color: #64748b; margin: 10px 0 20px 0;"><?php echo htmlspecialchars($task['description'] ?: 'No description provided.'); ?></p>
        
        <hr>
        <h3>Comments</h3>
        <div id="comments-thread">
            <?php 
            if ($comments_result && $comments_result->num_rows > 0) {
                while ($comment = $comments_result->fetch_assoc()) {
                    echo "<div class='comment-box' id='comment-" . $comment['id'] . "'>";
                    echo "  <div class='comment-meta'><b>" . htmlspecialchars($comment['author_name']) . "</b> &bull; " . $comment['created_at'] . "</div>";
                    echo "  <div class='comment-body'>" . htmlspecialchars($comment['body']) . "</div>";
                    
                    // Display delete option ONLY if the active session matches the comment author
                    if ($_SESSION['user_id'] == $comment['user_id']) {
                        echo "  <a class='delete-link' onclick='deleteComment(" . $comment['id'] . ")'>Delete</a>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p id='no-comments-msg' style='color: #94a3b8; font-style: italic; padding: 10px 0;'>No comments yet. Start the conversation!</p>";
            }
            ?>
        </div>

        <div class="comment-form">
            <h4>Leave a Comment</h4>
            <form id="ajax-comment-form">
                <input type="hidden" id="form-task-id" value="<?php echo $task_id; ?>">
                <input type="hidden" id="form-project-id" value="<?php echo $task['project_id']; ?>">
                
                <textarea id="comment-body-input" placeholder="Type your comment here..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
            <div id="form-error-output" style="color: #ef4444; font-size: 13px; margin-top: 8px; display: none;"></div>
        </div>
    </div>

    <script src="../assets/js/taskDetailActions.js"></script>
</body>
</html>