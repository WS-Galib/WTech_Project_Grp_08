<?php
$pageTitle = 'Home';
$activeNav = 'home';
require 'views/layout/header.php';
?>

<div class="ws-home-hero">
  <div>
    <h2>Welcome To The Workspace : <?= e($workspace['name']) ?></h2>
    <p><?= e($workspace['description'] ?: 'Collaborate and manage your projects here.') ?></p>
  </div>

  <div class="invite-code-box">
    <div>
      <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;opacity:0.8;margin-bottom:2px">
        Invite Code
      </div>
      <div class="code" id="inviteCodeText"><?= e($workspace['invite_code']) ?></div>
    </div>
    <button class="copy-btn" onclick="copyCode()">Copy</button>
  </div>
</div>

<div class="home-features-grid">
  <!-- Project Management -->
  <div class="home-feature-card">
    <div class="hf-icon">📁</div>
    <div class="hf-content">
      <h3>Project Management</h3>
      <p>Create, Organize and Manage projects effectively within your workspace.</p>
    </div>
    <a href="#" class="btn btn-primary">Go to Projects</a>
  </div>

  <!-- Settings (owner only) -->
  <?php if (!empty($workspace) && (int)$workspace['owner_id'] === (int)$_SESSION['user_id']): ?>
  <div class="home-feature-card">
    <div class="hf-icon">⚙️</div>
    <div class="hf-content">
      <h3>Settings</h3>
      <p>Manage workspace configurations, add and remove members.</p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=workspace.settings" class="btn btn-primary">Manage Settings</a>
  </div>
  <?php endif; ?>
</div>

<script>
function copyCode() {
  var code = document.getElementById('inviteCodeText').innerText;
  navigator.clipboard.writeText(code).then(function() {
    alert('Invite code copied to clipboard!');
  });
}
</script>

</main>
  </div><!-- /.main-content -->
</div><!-- /.app-shell -->
</body>
</html>
