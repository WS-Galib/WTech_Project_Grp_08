<?php


session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Workspace.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/WorkspaceController.php';

$auth      = new AuthController($mysqli);
$workspace = new WorkspaceController($mysqli);

$page   = $_GET['page'] ?? 'login';
$method = $_SERVER['REQUEST_METHOD'];

switch ($page) {

    case 'login':
        if ($method === 'POST') $auth->login();
        else                    $auth->showLogin();
        break;

    case 'register':
        if ($method === 'POST') $auth->register();
        else                    $auth->showRegister();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'workspace':
        $workspace->home();
        break;

    case 'workspace.setup':
        $workspace->showSetup();
        break;

    case 'workspace.create':
        if ($method === 'POST') $workspace->create();
        else                    redirect(BASE_URL . '?page=workspace.setup');
        break;

    case 'workspace.join':
        if ($method === 'POST') $workspace->join();
        else                    redirect(BASE_URL . '?page=workspace.setup');
        break;

    case 'workspace.settings':
        $workspace->showSettings();
        break;

    case 'workspace.switch':
        $wsId = (int) ($_GET['id'] ?? 0);
        $workspace->switchWorkspace($wsId);
        break;

    case 'api.workspace.create':
        if ($method === 'POST') $workspace->createQuick();
        break;

    case 'api.workspace.join':
        if ($method === 'POST') $workspace->joinAjax();
        break;

    case 'api.members.add':
        if ($method === 'POST') $workspace->addMemberByEmail();
        break;

    case 'api.members.delete':
        $memberId = (int) ($_GET['id'] ?? 0);
        if ($method === 'POST') $workspace->deleteMember($memberId);
        break;

    default:
        http_response_code(404);
        echo '<h2 style="font-family:sans-serif;text-align:center;margin-top:4rem">404 — Page not found</h2>';
}
