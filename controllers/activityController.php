<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["ok" => false, "error" => "Unauthenticated"]);
    exit();

}
include "../models/db.php";
$project_id = $_GET["project_id"] ?? "";
$filter_user_id = $_GET["user_id"] ?? "all";

if (empty($project_id)) {
    echo json_encode(["ok" => false, "error" => "Missing project ID"]);
    exit();
}
$database = new db();
$connection = $database->connection();
$sql = "SELECT a.action_text, a.created_at, u.name FROM activity_logs a JOIN users u ON a.user_id = u.id WHERE a.project_id = ?";
$params = [$project_id];
$types = "i";

if ($filter_user_id !== "all") {
    $sql .= " AND a.user_id = ?";
    $params[] = $filter_user_id;
    $types .= "i";
}
$sql .= " ORDER BY a.created_at DESC LIMIT 50";
$stmt = $connection->prepare($sql);

if ($filter_user_id !== "all") {
    $stmt->bind_param($types, $params[0], $params[1]);
}
else {
    $stmt->bind_param($types, $params[0]);
}
$stmt->execute();
$result = $stmt->get_result();
$activities = [];
while ($row = $result->fetch_assoc()) {
    $initials = "";
    $words = explode(" ", trim($row["name"]));
    foreach ($words as $w) {
        if (!empty($w)) $initials .= strtoupper($w[0]);
    }
    $timestamp = strtotime($row["created_at"]);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        $time_ago = "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        $time_ago = $mins . ($mins == 1 ? " minute ago" : " minutes ago");
    } elseif ($diff < 86400) {
        $hrs = floor($diff / 3600);
        $time_ago = $hrs . ($hrs == 1 ? " hour ago" : " hours ago");
    } else {
        $days = floor($diff / 86400);
        $time_ago = $days . ($days == 1 ? " day ago" : " days ago");
    }
    $activities[] = [
        "initials" => substr($initials, 0, 2), // Keep it to 2 letters max
        "action_text" => htmlspecialchars($row["action_text"]),
        "time_ago" => $time_ago
    ];
}
echo json_encode(["ok" => true, "data" => $activities]);
?>