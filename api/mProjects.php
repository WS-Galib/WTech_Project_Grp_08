<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    // Demo fallback — remove when Task 1 auth is integrated
    $_SESSION['user_id']      = 1;
    $_SESSION['workspace_id'] = 1;
}

require_once __DIR__ . '/../config/mDatabase.php';
require_once __DIR__ . '/../models/ProjectModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id'])     ? (int)$_GET['id']     : 0;
$action = $_GET['action'] ?? '';

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing project id']);
    exit;
}

$model = new ProjectModel();
$project = $model->getProject($id);

if (!$project) {
    http_response_code(404);
    echo json_encode(['error' => 'Project not found']);
    exit;
}

if ($method === 'POST') {
    if ($action === 'archive') {
        $model->archiveProject($id);
        echo json_encode(['success' => true, 'message' => 'Project archived successfully.']);
    } elseif ($action === 'unarchive') {
        $model->unarchiveProject($id);
        echo json_encode(['success' => true, 'message' => 'Project restored successfully.']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
