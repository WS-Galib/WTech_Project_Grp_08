<?php
$colors = [
    '#6366f1' => 'Indigo',
    '#10b981' => 'Emerald',
    '#f59e0b' => 'Amber',
    '#ef4444' => 'Rose',
    '#8b5cf6' => 'Violet',
];
$selectedColor   = $old['color_label'] ?? $project['color_label'] ?? '#6366f1';
$selectedMembers = !empty($old['members']) ? (array)$old['members'] : $projectMemberIds;
$flash           = $_GET['flash'] ?? '';
?>

<div class="page-header">
    <div>
        <a href="index.php?action=detail&id=<?= $project['id'] ?>" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Project
        </a>
        <h1 class="page-title">Project Settings</h1>
        <p class="page-sub"><?= htmlspecialchars($project['name']) ?></p>
    </div>
</div>

<?php if ($flash === 'updated'): ?>
<div class="alert alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Project settings saved.
</div>
<?php endif; ?>

<div class="form-container">

    <!-- Edit Form -->
    <form method="POST" action="index.php?action=update&id=<?= $project['id'] ?>" id="edit-project-form" novalidate>

        <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
            <label for="proj-name" class="form-label">Project Name <span class="required">*</span></label>
            <input type="text" id="proj-name" name="name" class="form-input"
                   value="<?= htmlspecialchars($old['name'] ?? $project['name']) ?>"
                   maxlength="150" required>
            <?php if (isset($errors['name'])): ?>
                <span class="error-msg"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="proj-desc" class="form-label">Description</label>
            <textarea id="proj-desc" name="description" class="form-input form-textarea"><?= htmlspecialchars($old['description'] ?? $project['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="proj-deadline" class="form-label">Deadline</label>
            <input type="date" id="proj-deadline" name="deadline" class="form-input"
                   value="<?= htmlspecialchars($old['deadline'] ?? $project['deadline'] ?? '') ?>">
        </div>

        <!-- Colour Label -->
        <div class="form-group">
            <label class="form-label">Colour Label</label>
            <div class="color-swatches" role="group">
                <?php foreach ($colors as $hex => $name): ?>
                <label class="swatch-label">
                    <input type="radio" name="color_label" value="<?= $hex ?>"
                           class="swatch-radio"
                           <?= $selectedColor === $hex ? 'checked' : '' ?>>
                    <span class="swatch" style="background:<?= $hex ?>" title="<?= $name ?>">
                        <svg class="swatch-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="swatch-name"><?= $name ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Members Multi-Select -->
        <div class="form-group <?= isset($errors['members']) ? 'has-error' : '' ?>">
            <label class="form-label">Team Members <span class="required">*</span></label>
            <p class="form-hint">Check members to include; unchecked members will be removed.</p>
            <div class="member-list" id="member-checkboxes">
                <?php foreach ($workspaceMembers as $m): ?>
                <label class="member-check-item" for="mem-<?= $m['id'] ?>">
                    <input type="checkbox" id="mem-<?= $m['id'] ?>"
                           name="members[]" value="<?= $m['id'] ?>"
                           <?= in_array((string)$m['id'], array_map('strval', $selectedMembers)) ? 'checked' : '' ?>>
                    <span class="member-avatar-wrap">
                        <span class="avatar-sm"><?= strtoupper(substr($m['name'],0,1)) ?></span>
                    </span>
                    <span class="member-info">
                        <span class="member-name"><?= htmlspecialchars($m['name']) ?></span>
                        <span class="member-email"><?= htmlspecialchars($m['email']) ?></span>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($errors['members'])): ?>
                <span class="error-msg"><?= htmlspecialchars($errors['members']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a href="index.php?action=list" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submit-edit">Save Changes</button>
        </div>
    </form>

    <!-- Danger Zone: Archive -->
    <div class="danger-zone">
        <h3 class="danger-title">Danger Zone</h3>
        <div class="danger-row">
            <div>
                <p class="danger-label">Archive this project</p>
                <p class="danger-desc">Archived projects are hidden from the active list but data is preserved.</p>
            </div>
            <button class="btn btn-danger btn-archive"
                    data-id="<?= $project['id'] ?>"
                    data-action="archive"
                    id="archive-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                Archive Project
            </button>
        </div>
    </div>

</div><!-- /.form-container -->
