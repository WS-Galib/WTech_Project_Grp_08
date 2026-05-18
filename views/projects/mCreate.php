<?php
$colors = [
    '#6366f1' => 'Indigo',
    '#10b981' => 'Emerald',
    '#f59e0b' => 'Amber',
    '#ef4444' => 'Rose',
    '#8b5cf6' => 'Violet',
];
$selectedColor = $old['color_label'] ?? '#6366f1';
$selectedMembers = isset($old['members']) ? (array)$old['members'] : [];
?>

<div class="page-header">
    <div>
        <a href="index.php?action=list" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Projects
        </a>
        <h1 class="page-title">Create New Project</h1>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="index.php?action=store" id="create-project-form" novalidate>

        <!-- Name -->
        <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
            <label for="proj-name" class="form-label">Project Name <span class="required">*</span></label>
            <input type="text" id="proj-name" name="name" class="form-input"
                   placeholder="e.g. Website Redesign"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                   maxlength="150" required>
            <?php if (isset($errors['name'])): ?>
                <span class="error-msg"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="proj-desc" class="form-label">Description</label>
            <textarea id="proj-desc" name="description" class="form-input form-textarea"
                      placeholder="Describe the project goals..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <!-- Deadline -->
        <div class="form-group">
            <label for="proj-deadline" class="form-label">Deadline</label>
            <input type="date" id="proj-deadline" name="deadline" class="form-input"
                   value="<?= htmlspecialchars($old['deadline'] ?? '') ?>">
        </div>

        <!-- Colour Label -->
        <div class="form-group">
            <label class="form-label">Colour Label</label>
            <div class="color-swatches" role="group" aria-label="colour label">
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

        <!-- Members -->
        <div class="form-group <?= isset($errors['members']) ? 'has-error' : '' ?>">
            <label class="form-label">Team Members <span class="required">*</span></label>
            <p class="form-hint">Select at least one workspace member.</p>
            <?php if (empty($workspaceMembers)): ?>
                <p class="form-hint warn">No workspace members found. Add members to the workspace first.</p>
            <?php else: ?>
            <div class="member-list" id="member-checkboxes">
                <?php foreach ($workspaceMembers as $m): ?>
                <label class="member-check-item" for="mem-<?= $m['id'] ?>">
                    <input type="checkbox" id="mem-<?= $m['id'] ?>"
                           name="members[]" value="<?= $m['id'] ?>"
                           <?= in_array($m['id'], $selectedMembers) ? 'checked' : '' ?>>
                    <span class="member-avatar-wrap">
                        <span class="avatar-sm">
                            <?= strtoupper(substr($m['name'], 0, 1)) ?>
                        </span>
                    </span>
                    <span class="member-info">
                        <span class="member-name"><?= htmlspecialchars($m['name']) ?></span>
                        <span class="member-email"><?= htmlspecialchars($m['email']) ?></span>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if (isset($errors['members'])): ?>
                <span class="error-msg"><?= htmlspecialchars($errors['members']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a href="index.php?action=list" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submit-create">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Project
            </button>
        </div>

    </form>
</div>
