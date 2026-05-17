<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register — Kanban-Board</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-brand">
    <div class="brand-logo">📋</div>
    <h1>Kanban-Board</h1>
    <p>Join thousands of teams who trust Kanban-Board to manage their projects and tasks.</p>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h2>💁🏼‍♂️ Create an account</h2>
      <p class="subtitle">Start collaborating with your team today.</p>

      <form action="<?= BASE_URL ?>/index.php?page=register" method="POST">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name"
            class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
            value="<?= e($old['name'] ?? '') ?>"
            placeholder="Washif Shadman">
          <?php if (!empty($errors['name'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['name']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <input type="text" name="email"
            class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= e($old['email'] ?? '') ?>"
            placeholder="name@company.com">
          <?php if (!empty($errors['email'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['email']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Password (min. 8 characters)</label>
          <input type="password" name="password"
            class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="••••••••">
          <?php if (!empty($errors['password'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['password']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="password_confirm"
            class="form-control <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>"
            placeholder="••••••••">
          <?php if (!empty($errors['password_confirm'])): ?>
            <div class="error-msg">⚠️ <?= e($errors['password_confirm']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-2">Create Account</button>
      </form>

      <div class="text-center mt-3 text-sm">
        <span class="text-muted">Already have an account?</span>
        <a href="<?= BASE_URL ?>/index.php?page=login" style="font-weight:600;">Sign In</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
