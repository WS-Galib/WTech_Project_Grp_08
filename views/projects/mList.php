<?php
// Helper: initials from name
function initials(string $name): string {
    $parts = explode(' ', trim($name));
    $i = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $i .= strtoupper(substr(end($parts), 0, 1));
    return $i;
}
$tab = $_GET['tab'] ?? 'active';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-sub">Manage your workspace projects</p>
    </div>
    <a href="index.php?action=create" class="btn btn-primary" id="create-project-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Project
    </a>
</div>

<!-- Tabs -->
<div class="tabs" role="tablist">
    <a href="index.php?action=list&tab=active"
       class="tab-btn <?= $tab !== 'archived' ? 'active' : '' ?>"
       role="tab" id="tab-active">
        Active
        <span class="tab-badge"><?= count($activeProjects) ?></span>
    </a>
    <a href="index.php?action=list&tab=archived"
       class="tab-btn <?= $tab === 'archived' ? 'active' : '' ?>"
       role="tab" id="tab-archived">
        Archived
        <span class="tab-badge"><?= count($archivedProjects) ?></span>
    </a>
</div>

<!-- Active Projects Grid -->
<?php if ($tab !== 'archived'): ?>
<div class="project-grid" id="active-grid">
    <?php if (empty($activeProjects)): ?>
        <div class="empty-state">
            <div class="empty-icon">📁</div>
            <h3>No active projects</h3>
            <p>Create your first project to get started.</p>
            <a href="index.php?action=create" class="btn btn-primary">Create Project</a>
        </div>
    <?php else: ?>
        <?php foreach ($activeProjects as $p):
            $total    = (int)$p['total_count'];
            $done     = (int)$p['done_count'];
            $progress = $total > 0 ? round($done / $total * 100) : 0;
            $overdue  = $p['deadline'] && strtotime($p['deadline']) < time();
        ?>
        <div class="project-card" id="project-card-<?= $p['id'] ?>">
            <div class="card-top">
                <span class="color-dot" style="background:<?= htmlspecialchars($p['color_label']) ?>"></span>
                <div class="card-actions">
                    <a href="index.php?action=detail&id=<?= $p['id'] ?>" class="icon-btn" title="View detail">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="index.php?action=edit&id=<?= $p['id'] ?>" class="icon-btn" title="Settings">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </a>
                    <button class="icon-btn btn-archive" data-id="<?= $p['id'] ?>" data-action="archive" title="Archive">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    </button>
                </div>
            </div>

            <a href="index.php?action=detail&id=<?= $p['id'] ?>" class="card-title-link">
                <h2 class="card-title"><?= htmlspecialchars($p['name']) ?></h2>
            </a>

            <?php if ($p['deadline']): ?>
            <p class="card-deadline <?= $overdue ? 'overdue' : '' ?>">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= date('M j, Y', strtotime($p['deadline'])) ?>
                <?= $overdue ? '<span class="badge-overdue">Overdue</span>' : '' ?>
            </p>
            <?php endif; ?>

            <!-- Member Avatars -->
            <?php if (!empty($p['members'])): ?>
            <div class="member-stack">
                <?php foreach (array_slice($p['members'], 0, 5) as $m): ?>
                <div class="avatar-sm" title="<?= htmlspecialchars($m['name']) ?>">
                    <?= initials($m['name']) ?>
                </div>
                <?php endforeach; ?>
                <?php if (count($p['members']) > 5): ?>
                <div class="avatar-sm avatar-more">+<?= count($p['members']) - 5 ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Progress Bar -->
            <div class="progress-section">
                <div class="progress-label">
                    <?php if ($total === 0): ?>
                        <span class="no-tasks">No tasks yet</span>
                    <?php else: ?>
                        <span><?= $done ?>/<?= $total ?> done</span>
                        <span><?= $progress ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill"
                         style="width:<?= $progress ?>%; background:<?= htmlspecialchars($p['color_label']) ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Archived Projects Grid -->
<?php else: ?>
<div class="project-grid" id="archived-grid">
    <?php if (empty($archivedProjects)): ?>
        <div class="empty-state">
            <div class="empty-icon">🗄️</div>
            <h3>No archived projects</h3>
            <p>Archived projects will appear here.</p>
        </div>
    <?php else: ?>
        <?php foreach ($archivedProjects as $p):
            $total    = (int)$p['total_count'];
            $done     = (int)$p['done_count'];
            $progress = $total > 0 ? round($done / $total * 100) : 0;
        ?>
        <div class="project-card archived-card" id="project-card-<?= $p['id'] ?>">
            <div class="card-top">
                <span class="color-dot" style="background:<?= htmlspecialchars($p['color_label']) ?>"></span>
                <span class="badge-archived">Archived</span>
                <button class="icon-btn btn-unarchive" data-id="<?= $p['id'] ?>" data-action="unarchive" title="Restore">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.68"/></svg>
                </button>
            </div>
            <h2 class="card-title"><?= htmlspecialchars($p['name']) ?></h2>
            <?php if ($p['deadline']): ?>
            <p class="card-deadline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= date('M j, Y', strtotime($p['deadline'])) ?>
            </p>
            <?php endif; ?>
            <div class="member-stack">
                <?php foreach (array_slice($p['members'], 0, 5) as $m): ?>
                <div class="avatar-sm" title="<?= htmlspecialchars($m['name']) ?>"><?= initials($m['name']) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="progress-section">
                <div class="progress-label">
                    <?php if ($total === 0): ?>
                        <span class="no-tasks">No tasks yet</span>
                    <?php else: ?>
                        <span><?= $done ?>/<?= $total ?> done</span><span><?= $progress ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:<?= $progress ?>%; background:<?= htmlspecialchars($p['color_label']) ?>"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>
