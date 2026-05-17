<?php
require_once __DIR__ . '/../config/mDatabase.php';

class ProjectModel {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getActiveProjects(int $workspaceId): array {
        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done')        AS done_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id)                         AS total_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'todo')   AS todo_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'in-progress') AS inprogress_count
                FROM projects p
                WHERE p.workspace_id = :wid AND p.is_archived = 0
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':wid' => $workspaceId]);
        return $stmt->fetchAll();
    }

    public function getArchivedProjects(int $workspaceId): array {
        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done') AS done_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id)                       AS total_count
                FROM projects p
                WHERE p.workspace_id = :wid AND p.is_archived = 1
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':wid' => $workspaceId]);
        return $stmt->fetchAll();
    }

    public function getProject(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getProjectDetail(int $id): ?array {
        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'todo')        AS todo_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'in-progress') AS inprogress_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done')        AS done_count,
                    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id)                              AS total_count
                FROM projects p WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getProjectMembers(int $projectId): array {
        $sql = "SELECT u.id, u.name, u.email
                FROM project_members pm
                JOIN users u ON u.id = pm.user_id
                WHERE pm.project_id = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $projectId]);
        return $stmt->fetchAll();
    }

    public function getProjectMembersWithTaskCount(int $projectId): array {
        $sql = "SELECT u.id, u.name, u.email,
                    COUNT(t.id) AS task_count
                FROM project_members pm
                JOIN users u ON u.id = pm.user_id
                LEFT JOIN tasks t ON t.assigned_to = u.id AND t.project_id = :pid
                WHERE pm.project_id = :pid2
                GROUP BY u.id, u.name, u.email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $projectId, ':pid2' => $projectId]);
        return $stmt->fetchAll();
    }

    public function getWorkspaceMembers(int $workspaceId): array {
        $sql = "SELECT u.id, u.name, u.email
                FROM workspace_members wm
                JOIN users u ON u.id = wm.user_id
                WHERE wm.workspace_id = :wid
                ORDER BY u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':wid' => $workspaceId]);
        return $stmt->fetchAll();
    }

    public function createProject(array $data): int {
        $sql = "INSERT INTO projects (workspace_id, name, description, deadline, color_label)
                VALUES (:workspace_id, :name, :description, :deadline, :color_label)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':workspace_id' => $data['workspace_id'],
            ':name'         => $data['name'],
            ':description'  => $data['description'],
            ':deadline'     => $data['deadline'] ?: null,
            ':color_label'  => $data['color_label'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateProject(int $id, array $data): void {
        $sql = "UPDATE projects SET name=:name, description=:description,
                    deadline=:deadline, color_label=:color_label WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'        => $data['name'],
            ':description' => $data['description'],
            ':deadline'    => $data['deadline'] ?: null,
            ':color_label' => $data['color_label'],
            ':id'          => $id,
        ]);
    }

    public function replaceProjectMembers(int $projectId, array $userIds): void {
        $this->db->prepare("DELETE FROM project_members WHERE project_id = :pid")
                 ->execute([':pid' => $projectId]);
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO project_members (project_id, user_id) VALUES (:pid, :uid)"
        );
        foreach ($userIds as $uid) {
            $stmt->execute([':pid' => $projectId, ':uid' => (int)$uid]);
        }
    }

    public function archiveProject(int $id): void {
        $this->db->prepare("UPDATE projects SET is_archived = 1 WHERE id = :id")
                 ->execute([':id' => $id]);
    }

    public function unarchiveProject(int $id): void {
        $this->db->prepare("UPDATE projects SET is_archived = 0 WHERE id = :id")
                 ->execute([':id' => $id]);
    }
}
