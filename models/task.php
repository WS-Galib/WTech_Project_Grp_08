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
}
