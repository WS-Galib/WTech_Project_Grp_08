<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Kanban-Board') ?> — Kanban-Board</title>
  <meta name="description" content="Collaborative workspace and project management tool.">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">

    <a href="<?= BASE_URL ?>/index.php?page=workspace" class="sidebar-logo">
      <div class="logo-icon">📋</div>
      <span>Kanban-Board</span>
    </a>

    <div class="ws-switcher-block">
      <div class="ws-switcher-label">Workspace</div>

      <?php if (!empty($workspaces) && !empty($_SESSION['workspace_id'])): ?>
      <select id="wsSwitcher" class="ws-select" onchange="handleWsSwitch(this.value)">
        <?php foreach ($workspaces as $ws): ?>
          <option value="<?= (int)$ws['id'] ?>"
            <?= (int)$ws['id'] === (int)$_SESSION['workspace_id'] ? 'selected' : '' ?>>
            <?= e($ws['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php else: ?>
      <div class="ws-empty-hint">No workspace joined yet.</div>
      <?php endif; ?>

      <div class="ws-action-btns">
        <button class="ws-action-btn" id="btnCreateWs" onclick="toggleCreatePanel()" title="Create New Workspace">
          <span class="ws-btn-icon">＋</span> Create New
        </button>
        <button class="ws-action-btn ws-action-btn--join" id="btnJoinWs" onclick="toggleJoinPanel()" title="Join Another Workspace">
          <span class="ws-btn-icon">🔗</span> Join Another
        </button>
      </div>

      <div class="ws-join-panel" id="wsCreatePanel" style="display:none;">
        <div class="ws-join-panel-inner">
          <div class="ws-panel-title">New Workspace</div>
          <input
            type="text"
            id="wsCreateName"
            class="ws-join-input"
            placeholder="Workspace name *"
            maxlength="100"
            autocomplete="off"
            style="letter-spacing:0;font-family:inherit;text-transform:none;"
          >
          <div class="ws-join-error" id="wsCreateError" style="display:none;"></div>
          <textarea
            id="wsCreateDesc"
            class="ws-join-input ws-join-textarea"
            placeholder="Description (optional)"
            maxlength="500"
            rows="2"
          ></textarea>
          <div class="ws-join-actions">
            <button class="ws-join-submit" id="wsCreateSubmit" onclick="submitCreateWorkspace()">Create</button>
            <button class="ws-join-cancel" onclick="toggleCreatePanel()">Cancel</button>
          </div>
        </div>
      </div>

      <div class="ws-join-panel" id="wsJoinPanel" style="display:none;">
        <div class="ws-join-panel-inner">
          <div class="ws-panel-title">Join by Invite Code</div>
          <input
            type="text"
            id="wsJoinCode"
            class="ws-join-input"
            placeholder="Enter invite code…"
            maxlength="10"
            autocomplete="off"
            style="text-transform:uppercase;letter-spacing:2px;font-family:monospace;"
          >
          <div class="ws-join-error" id="wsJoinError" style="display:none;">Enter a valid code.</div>
          <div class="ws-join-actions">
            <button class="ws-join-submit" id="wsJoinSubmit" onclick="submitJoinCode()">Join</button>
            <button class="ws-join-cancel" onclick="toggleJoinPanel()">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <nav>
      <a href="<?= BASE_URL ?>/index.php?page=workspace" class="<?= ($activeNav ?? '') === 'home' ? 'active' : '' ?>">
        <span class="nav-icon">🏠</span> Home
      </a>
      <a href="#" class="<?= ($activeNav ?? '') === 'projects' ? 'active' : '' ?>">
        <span class="nav-icon">📁</span> Project Management
      </a>
      <?php if (!empty($workspace) && (int)$workspace['owner_id'] === (int)$_SESSION['user_id']): ?>
      <a href="<?= BASE_URL ?>/index.php?page=workspace.settings" class="<?= ($activeNav ?? '') === 'settings' ? 'active' : '' ?>">
        <span class="nav-icon">⚙️</span> Settings
      </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="avatar"
          style="background:<?= avatarColor($_SESSION['name'] ?? 'U') ?>">
          <?= e(initials($_SESSION['name'] ?? 'U')) ?>
        </div>
        <div class="user-info">
          <div class="user-name"><?= e($_SESSION['name'] ?? '') ?></div>
          <div class="user-role">Member</div>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=logout" class="logout-btn" title="Logout">⛔</a>
      </div>
    </div>

  </aside>

  <div class="main-content">
    <header class="topbar">
      <span class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></span>
      <div class="d-flex align-center gap-2">
        <a href="<?= BASE_URL ?>/index.php?page=logout" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </header>

    <main class="page-body">

<script>
function handleWsSwitch(wsId) {
  if (!wsId) return;
  window.location.href = '<?= BASE_URL ?>/index.php?page=workspace.switch&id=' + wsId;
}

function toggleCreatePanel() {
  var panel     = document.getElementById('wsCreatePanel');
  var joinPanel = document.getElementById('wsJoinPanel');
  var isVisible = panel.style.display !== 'none';

  joinPanel.style.display = 'none';
  panel.style.display = isVisible ? 'none' : 'block';

  if (!isVisible) {
    document.getElementById('wsCreateName').value = '';
    document.getElementById('wsCreateDesc').value = '';
    document.getElementById('wsCreateError').style.display = 'none';
    document.getElementById('wsCreateName').focus();
  }
}

function submitCreateWorkspace() {
  var name      = document.getElementById('wsCreateName').value.trim();
  var desc      = document.getElementById('wsCreateDesc').value.trim();
  var errEl     = document.getElementById('wsCreateError');
  var submitBtn = document.getElementById('wsCreateSubmit');

  errEl.style.display = 'none';

  if (!name) {
    errEl.textContent = 'Workspace name is required.';
    errEl.style.display = 'block';
    document.getElementById('wsCreateName').focus();
    return;
  }

  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ Creating…';

  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= BASE_URL ?>/index.php?page=api.workspace.create', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      try {
        var data = JSON.parse(xhr.responseText);
        if (data.ok && data.redirect) {
          window.location.href = data.redirect;
        } else {
          errEl.textContent = data.message || 'Could not create workspace.';
          errEl.style.display = 'block';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Create';
        }
      } catch (e) {
        errEl.textContent = 'Unexpected error. Try again.';
        errEl.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create';
      }
    }
  };
  xhr.send(
    'name='        + encodeURIComponent(name) +
    '&description=' + encodeURIComponent(desc)
  );
}

function toggleJoinPanel() {
  var panel        = document.getElementById('wsJoinPanel');
  var createPanel  = document.getElementById('wsCreatePanel');
  var isVisible    = panel.style.display !== 'none';

  createPanel.style.display = 'none';
  panel.style.display = isVisible ? 'none' : 'block';

  if (!isVisible) {
    document.getElementById('wsJoinCode').value = '';
    document.getElementById('wsJoinError').style.display = 'none';
    document.getElementById('wsJoinCode').focus();
  }
}

function submitJoinCode() {
  var code      = document.getElementById('wsJoinCode').value.trim().toUpperCase();
  var errEl     = document.getElementById('wsJoinError');
  var submitBtn = document.getElementById('wsJoinSubmit');

  errEl.style.display = 'none';

  if (!code) {
    errEl.textContent = 'Enter a valid code.';
    errEl.style.display = 'block';
    return;
  }

  submitBtn.disabled = true;
  submitBtn.textContent = '…';

  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= BASE_URL ?>/index.php?page=api.workspace.join', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      try {
        var data = JSON.parse(xhr.responseText);
        if (data.ok && data.redirect) {
          window.location.href = data.redirect;
        } else {
          errEl.textContent = data.message || 'Enter a valid code.';
          errEl.style.display = 'block';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Join';
        }
      } catch (e) {
        errEl.textContent = 'Unexpected error. Try again.';
        errEl.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Join';
      }
    }
  };
  xhr.send('invite_code=' + encodeURIComponent(code));
}

document.addEventListener('DOMContentLoaded', function () {
  var joinInput = document.getElementById('wsJoinCode');
  if (joinInput) {
    joinInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') submitJoinCode();
    });
  }
  var createInput = document.getElementById('wsCreateName');
  if (createInput) {
    createInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') submitCreateWorkspace();
    });
  }
});
</script>
