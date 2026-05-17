<?php
function initials(string $name): string {
    $parts = explode(' ', trim($name));
    $i = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $i .= strtoupper(substr(end($parts), 0, 1));
    return $i;
}
$total    = (int)$project['total_count'];
$done     = (int)$project['done_count'];
$inprog   = (int)$project['inprogress_count'];
$todo     = (int)$project['todo_count'];
$progress = $total > 0 ? round($done / $total * 100) : 0;
$overdue  = $project['deadline'] && strtotime($project['deadline']) < time();
?>

<div class="page-header">
    <div>
        <a href="index.php?action=list" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            All Projects
        </a>
        <div class="detail-title-row">
            <span class="color-dot large" style="background:<?= htmlspecialchars($project['color_label']) ?>"></span>
            <h1 class="page-title"><?= htmlspecialchars($project['name']) ?></h1>
        </div>
    </div>
    <a href="index.php?action=edit&id=<?= $project['id'] ?>" class="btn btn-secondary" id="btn-settings">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        Settings
    </a>
</div>

<div class="detail-grid">

    <!-- Left Column -->
    <div class="detail-main">

        <!-- Description -->
        <?php if (!empty($project['description'])): ?>
        <div class="detail-card">
            <h2 class="section-title">Description</h2>
            <p class="detail-desc"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Deadline -->
        <?php if ($project['deadline']): ?>
        <div class="detail-card">
            <h2 class="section-title">Deadline</h2>
            <p class="card-deadline <?= $overdue ? 'overdue' : '' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= date('F j, Y', strtotime($project['deadline'])) ?>
                <?= $overdue ? '<span class="badge-overdue">Overdue</span>' : '' ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Task Summary -->
        <div class="detail-card">
            <h2 class="section-title">Task Summary</h2>
            <?php if ($total === 0): ?>
                <p class="no-tasks">No tasks yet. Tasks are managed by the Task Board (Student 3).</p>
            <?php else: ?>
            <div class="task-badges">
                <div class="task-badge badge-todo">
                    <span class="badge-count"><?= $todo ?></span>
                    <span class="badge-label">To Do</span>
                </div>
                <div class="task-badge badge-inprogress">
                    <span class="badge-count"><?= $inprog ?></span>
                    <span class="badge-label">In Progress</span>
                </div>
                <div class="task-badge badge-done">
                    <span class="badge-count"><?= $done ?></span>
                    <span class="badge-label">Done</span>
                </div>
            </div>
            <div class="progress-section" style="margin-top:1rem;">
                <div class="progress-label">
                    <span><?= $done ?>/<?= $total ?> tasks done</span>
                    <span><?= $progress ?>%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill"
                         style="width:<?= $progress ?>%; background:<?= htmlspecialchars($project['color_label']) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Members -->
    <div class="detail-sidebar">
        <div class="detail-card">
            <h2 class="section-title">
                Team Members
                <span class="tab-badge" style="margin-left:.5rem"><?= count($members) ?></span>
            </h2>
            <?php if (empty($members)): ?>
                <p class="no-tasks">No members assigned.</p>
            <?php else: ?>
            <ul class="member-detail-list">
                <?php foreach ($members as $m): ?>
                <li class="member-detail-item">
                    <div class="avatar-md"><?= initials($m['name']) ?></div>
                    <div class="member-info">
                        <span class="member-name"><?= htmlspecialchars($m['name']) ?></span>
                        <span class="member-email"><?= htmlspecialchars($m['email']) ?></span>
                    </div>
                    <span class="task-count-badge" title="Assigned tasks">
                        <?= (int)$m['task_count'] ?> task<?= $m['task_count'] != 1 ? 's' : '' ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <a href="index.php?action=edit&id=<?= $project['id'] ?>" class="btn btn-ghost btn-sm" style="margin-top:1rem;width:100%;">
                Manage Members
            </a>
        </div>
    </div>

</div><!-- /.detail-grid -->
