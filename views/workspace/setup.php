<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Setup Workspace — Kanban-Board</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="setup-page">
  <div class="setup-header">
    <div class="logo-icon">😎</div>
    <h1>Welcome, <?= e($_SESSION['name']) ?>!</h1>
    <p>Let's get you set up. Create a new workspace or join an existing one.</p>
  </div>

  <div class="setup-grid">
    <!-- Create Workspace -->
    <div class="setup-card">
      <div class="setup-icon">🏢</div>
      <h3>Create a new workspace</h3>
      <p class="setup-desc">Start fresh and invite your team members.</p>

      <form action="<?= BASE_URL ?>/index.php?page=workspace.create" method="POST">
        <div class="form-group">
          <label>Workspace Name</label>
          <input type="text" name="name"
            class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
            value="<?= e($old['name'] ?? '') ?>"
            placeholder="e.g. AIUB Web Tech Team">
          <?php if (!empty($errors['name'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['name']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Description (Optional)</label>
          <textarea name="description" class="form-control" rows="2"
            placeholder="What is this workspace for?"><?= e($old['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Create Workspace</button>
      </form>
    </div>

    <!-- Join Workspace -->
    <div class="setup-card">
      <div class="setup-icon">🤝</div>
      <h3>Join existing workspace</h3>
      <p class="setup-desc">Enter an invite code provided by your team lead.</p>

      <form action="<?= BASE_URL ?>/index.php?page=workspace.join" method="POST">
        <div class="form-group">
          <label>Invite Code</label>
          <input type="text" name="invite_code"
            class="form-control <?= !empty($errors['invite_code']) ? 'is-invalid' : '' ?>"
            value="<?= e($old['invite_code'] ?? '') ?>"
            placeholder="e.g. A1B2C3"
            style="text-transform: uppercase; font-family: monospace; letter-spacing: 2px;">
          <?php if (!empty($errors['invite_code'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['invite_code']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-outline btn-block mt-2">Join Workspace</button>
      </form>

      <?php if (!empty($workspaces)): ?>
        <div class="divider">OR</div>
        <a href="<?= BASE_URL ?>/index.php?page=workspace" class="btn btn-ghost btn-block">
          Return to Dashboard
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
