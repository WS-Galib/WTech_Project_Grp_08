<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../views/login.php");
    exit();
}
$project_id = $_GET["project_id"] ?? "";
if (empty($project_id)) {
    header("Location: ../views/dashboard.php");
    exit();
}
include "../models/db.php";
$database = new db();
$connection = $database->connection();

$members_query = "SELECT u.id, u.name FROM project_members pm JOIN users u ON pm.user_id = u.id WHERE pm.project_id = ?";
$stmt = $connection->prepare($members_query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$members_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Project Activity Feed</title>
    <link rel="stylesheet" href="../assets/css/taskBoard.css">
    <style>
        .activity-container { max-width: 650px; margin: 40px auto; background: white; padding: 30px; border-radius: 14px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; }
        .filter-bar select { padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: #f8fafc; font-weight: 500; }
        .activity-item { display: flex; align-items: flex-start; padding: 16px 0; border-bottom: 1px solid #f1f5f9; }
        .activity-item:last-child { border-bottom: none; }
        .avatar { width: 42px; height: 42px; background: #6366f1; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 15px; margin-right: 18px; flex-shrink: 0; }
        .activity-content { flex-grow: 1; }
        .activity-text { font-size: 15px; color: #1e293b; margin-bottom: 6px; }
        .activity-time { font-size: 12px; color: #64748b; font-weight: 500; }
    </style>
</head>
<body>
    <div class="activity-container">
        <a href="taskBoard.php?project_id=<?php echo $project_id; ?>" style="color: #4f46e5; text-decoration: none; display: inline-block; margin-bottom: 15px; font-weight: 600;">&larr; Back to Board</a>
        
        <div class="filter-bar">
            <h2>Activity Feed</h2>
            <select id="user-filter">
                <option value="all">All Members</option>
                <?php
                if ($members_result && $members_result->num_rows > 0) {
                    while ($row = $members_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div id="activity-list"></div>        
        <input type="hidden" id="current-project-id" value="<?php echo htmlspecialchars($project_id); ?>">
    </div>
    <script src="../assets/js/activityFeed.js"></script>
</body>
</html>