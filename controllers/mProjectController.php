<?php
require_once __DIR__ . '/../models/ProjectModel.php';

class mProjectController {
    private ProjectModel $model;
    private array $allowedColors = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6'];

    public function __construct() {
        $this->model = new ProjectModel();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'list';
        $id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

        match($action) {
            'create' => $this->create(),
            'store'  => $this->store(),
            'detail' => $this->detail($id),
            'edit'   => $this->edit($id),
            'update' => $this->update($id),
            default  => $this->list(),
        };
    }

    /* ── LIST ── */
    private function list(): void {
        $wid = (int)$_SESSION['workspace_id'];
        $activeProjects   = $this->model->getActiveProjects($wid);
        $archivedProjects = $this->model->getArchivedProjects($wid);

        foreach ($activeProjects   as &$p) { $p['members'] = $this->model->getProjectMembers($p['id']); }
        foreach ($archivedProjects as &$p) { $p['members'] = $this->model->getProjectMembers($p['id']); }

        $this->render('list', compact('activeProjects', 'archivedProjects'));
    }

    /* ── CREATE FORM ── */
    private function create(): void {
        $wid = (int)$_SESSION['workspace_id'];
        $workspaceMembers = $this->model->getWorkspaceMembers($wid);
        $errors = [];
        $old    = [];
        $this->render('create', compact('workspaceMembers', 'errors', 'old'));
    }

    /* ── STORE (POST) ── */
    private function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=create'); exit;
        }

        $wid         = (int)$_SESSION['workspace_id'];
        $errors      = [];
        $old         = $_POST;
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deadline    = $_POST['deadline'] ?? '';
        $colorLabel  = $_POST['color_label'] ?? '#6366f1';
        $members     = $_POST['members'] ?? [];

        if ($name === '')        $errors['name']    = 'Project name is required.';
        if (strlen($name) > 150) $errors['name']    = 'Project name must be under 150 characters.';
        if (empty($members))     $errors['members'] = 'At least one member must be selected.';
        if (!in_array($colorLabel, $this->allowedColors)) $colorLabel = '#6366f1';

        if (!empty($errors)) {
            $workspaceMembers = $this->model->getWorkspaceMembers($wid);
            $this->render('create', compact('workspaceMembers', 'errors', 'old'));
            return;
        }

        $projectId = $this->model->createProject([
            'workspace_id' => $wid,
            'name'         => $name,
            'description'  => $description,
            'deadline'     => $deadline,
            'color_label'  => $colorLabel,
        ]);
        $this->model->replaceProjectMembers($projectId, $members);

        header('Location: index.php?action=list&flash=created'); exit;
    }

    /* ── DETAIL ── */
    private function detail(?int $id): void {
        if (!$id) { header('Location: index.php'); exit; }
        $project = $this->model->getProjectDetail($id);
        if (!$project) { header('Location: index.php'); exit; }
        $members = $this->model->getProjectMembersWithTaskCount($id);
        $this->render('detail', compact('project', 'members'));
    }

    /* ── EDIT FORM ── */
    private function edit(?int $id): void {
        if (!$id) { header('Location: index.php'); exit; }
        $project = $this->model->getProject($id);
        if (!$project) { header('Location: index.php'); exit; }

        $wid              = (int)$_SESSION['workspace_id'];
        $workspaceMembers = $this->model->getWorkspaceMembers($wid);
        $projectMemberIds = array_column($this->model->getProjectMembers($id), 'id');
        $errors           = [];
        $old              = $project;
        $this->render('edit', compact('project', 'workspaceMembers', 'projectMemberIds', 'errors', 'old'));
    }

    /* ── UPDATE (POST) ── */
    private function update(?int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: index.php'); exit;
        }
        $project = $this->model->getProject($id);
        if (!$project) { header('Location: index.php'); exit; }

        $wid         = (int)$_SESSION['workspace_id'];
        $errors      = [];
        $old         = $_POST;
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deadline    = $_POST['deadline'] ?? '';
        $colorLabel  = $_POST['color_label'] ?? '#6366f1';
        $members     = $_POST['members'] ?? [];

        if ($name === '')        $errors['name']    = 'Project name is required.';
        if (strlen($name) > 150) $errors['name']    = 'Project name must be under 150 characters.';
        if (empty($members))     $errors['members'] = 'At least one member must be selected.';
        if (!in_array($colorLabel, $this->allowedColors)) $colorLabel = '#6366f1';

        if (!empty($errors)) {
            $workspaceMembers = $this->model->getWorkspaceMembers($wid);
            $projectMemberIds = $members;
            $this->render('edit', compact('project', 'workspaceMembers', 'projectMemberIds', 'errors', 'old'));
            return;
        }

        $this->model->updateProject($id, compact('name', 'description', 'deadline', 'color_label') + ['color_label' => $colorLabel]);
        $this->model->replaceProjectMembers($id, $members);

        header("Location: index.php?action=edit&id={$id}&flash=updated"); exit;
    }

    /* ── HELPER ── */
    private function render(string $view, array $data = []): void {
        extract($data);
        require __DIR__ . '/../views/layout/mHeader.php';
        require __DIR__ . "/../views/projects/m" . ucfirst($view) . ".php";
        require __DIR__ . '/../views/layout/mFooter.php';
    }
}
