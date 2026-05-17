<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login — Kanban-Board</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-brand">
    <div class="brand-logo">📋</div>
    <h1>Kanban-Board</h1>
    <p>The collaborative workspace where teams build and track projects faster.</p>
    <ul class="feature-list">
      <li>Organize projects with Kanban boards</li>
      <li>Real-time team collaboration</li>
      <li>Track progress and activity logs</li>
    </ul>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h2>👽 Great to see you again !</h2>
      <p class="subtitle">Enter your details to access your workspace.</p>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger">
          <?= e($errors['general']) ?>
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/index.php?page=login" method="POST">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email"
            class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= e($old['email'] ?? '') ?>"
            placeholder="name@company.com">
          <?php if (!empty($errors['email'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['email']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password"
            class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="••••••••">
          <?php if (!empty($errors['password'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['password']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-2">Sign In</button>
      </form>

      <div class="text-center mt-3 text-sm">
        <span class="text-muted">Don't have an account?</span>
        <a href="<?= BASE_URL ?>/index.php?page=register" style="font-weight:600;">Create one</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
