<?php
$action    = $_GET['action'] ?? 'list';
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$flash     = $_GET['flash'] ?? '';

$pageTitle = match($action) {
    'create' => 'New Project',
    'edit'   => 'Project Settings',
    'detail' => 'Project Detail',
    default  => 'Projects',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ProjectHub – Project Management for your workspace team.">
    <title><?= htmlspecialchars($pageTitle) ?> | ProjectHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="brand">
            <span class="brand-hex">&#x2B21;</span>
            <span class="brand-name">ProjectHub</span>
        </a>

        <div class="nav-links">
            <a href="index.php?action=list"
               class="nav-link <?= $action === 'list' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                All Projects
            </a>

            <a href="index.php?action=create" id="btn-new-project"
               class="btn btn-primary btn-sm <?= $action === 'create' ? 'active' : '' ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                New Project
            </a>
        </div>

        <div class="nav-user">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
        </div>
    </div>
</nav>

<?php if ($flash === 'created'): ?>
    <div class="toast toast-success" id="flash-toast">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Project created successfully!
    </div>
<?php elseif ($flash === 'updated'): ?>
    <div class="toast toast-success" id="flash-toast">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Project updated successfully!
    </div>
<?php endif; ?>

<main class="main-content">
