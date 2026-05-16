<?php
class task
{
    function createTask($connection, $tablename, $project_id, $title, $description, $assigned_to, $priority, $due_date)
    {
        $sql = "INSERT INTO " . $tablename . " (project_id, title, description, assigned_to, priority, due_date, status) VALUES ('" . $project_id . "', '" . $title . "', '" . $description . "', '" . $assigned_to . "', '" . $priority . "', '" . $due_date . "', 'todo')";
        $result = $connection->query($sql);
        return $result;
    }

    function getTasksByStatus($connection, $tablename, $project_id, $status)
    {
        $sql = "SELECT * FROM " . $tablename . " WHERE project_id='" . $project_id . "' AND status='" . $status . "'";
        $result = $connection->query($sql);
        return $result;
    }

    function updateTaskStatus($connection, $tablename, $task_id, $new_status)
    {
        $sql = "UPDATE " . $tablename . " SET status='" . $new_status . "' WHERE id='" . $task_id . "'";
        $result = $connection->query($sql);
        return $result;
    }

    function getProjectMembers($connection, $project_id)
    {
        $sql = "SELECT pm.user_id, u.name FROM project_members pm LEFT JOIN users u ON pm.user_id = u.id WHERE pm.project_id='" . $project_id . "'";
        $result = $connection->query($sql);
        return $result;
    }
}
