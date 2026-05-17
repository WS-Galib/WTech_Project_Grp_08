<?php
if (!function_exists('log_activity')) {
    function log_activity($connection, $project_id, $user_id, $action_text) {
        $sql = "INSERT INTO activity_logs (project_id, user_id, action_text, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $connection->prepare($sql);        
        if ($stmt) {
            $stmt->bind_param("iis", $project_id, $user_id, $action_text);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }
}
?>