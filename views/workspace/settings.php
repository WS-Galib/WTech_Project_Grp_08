<?php
$pageTitle = 'Workspace Settings';
$activeNav = 'settings';
require 'views/layout/header.php';
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
  <div class="card-header">
    <h2 class="card-title">Members Management</h2>
    <span class="badge badge-owner">Owner View</span>
  </div>

  <div class="add-member-section" style="padding: 1.5rem; border-bottom: 1px solid var(--border); background: #f8fafc;">
    <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">Add New Member</h3>
    <p class="text-muted text-sm" style="margin-bottom: 1rem;">Enter the email address of a registered user to add them to this workspace.</p>
    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
      <div style="flex: 1;">
        <input type="email" id="newMemberEmail" class="form-control" placeholder="user@example.com" required>
        <div class="error-msg" id="addMemberError" style="display:none; margin-top: 0.4rem;"></div>
      </div>
      <button class="btn btn-primary" id="addMemberBtn" onclick="addMember()">Add Member</button>
    </div>
  </div>

  <table class="members-table" id="membersTable">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Joined</th>
        <th style="text-align:right">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($members as $m): ?>
      <tr data-member-id="<?= $m['member_id'] ?>">
        <td>
          <div class="member-info">
            <div class="avatar" style="background:<?= avatarColor($m['name']) ?>">
              <?= e(initials($m['name'])) ?>
            </div>
            <span style="font-weight:500; color:var(--text-primary)">
              <?= e($m['name']) ?>
              <?php if ((int)$m['user_id'] === (int)$_SESSION['user_id']): ?>
                <span class="text-muted text-sm">(You)</span>
              <?php endif; ?>
            </span>
          </div>
        </td>
        <td><?= e($m['email']) ?></td>
        <td>
          <?php if ((int)$m['user_id'] === (int)$workspace['owner_id']): ?>
            <span class="badge badge-owner">Owner</span>
          <?php else: ?>
            <span class="badge badge-member">Member</span>
          <?php endif; ?>
        </td>
        <td class="text-muted"><?= date('M j, Y', strtotime($m['joined_at'])) ?></td>
        <td style="text-align:right">
          <?php if ((int)$m['user_id'] !== (int)$_SESSION['user_id']): ?>
            <button class="btn btn-danger btn-sm" onclick="removeMember(<?= $m['member_id'] ?>, this)">
              Remove
            </button>
          <?php else: ?>
            <span class="text-muted text-sm">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function addMember() {
  var emailInput = document.getElementById('newMemberEmail');
  var errorEl = document.getElementById('addMemberError');
  var btn = document.getElementById('addMemberBtn');
  var email = emailInput.value.trim();

  errorEl.style.display = 'none';

  if (!email) {
    errorEl.textContent = 'Enter a valid email.';
    errorEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Adding...';

  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= BASE_URL ?>/index.php?page=api.members.add', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          if (data.ok) {
            window.location.reload();
          } else {
            errorEl.textContent = data.message || 'Enter a valid email.';
            errorEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Add Member';
          }
        } catch (e) {
          errorEl.textContent = 'Unexpected error occurred.';
          errorEl.style.display = 'block';
          btn.disabled = false;
          btn.textContent = 'Add Member';
        }
      } else {
        try {
            var data = JSON.parse(xhr.responseText);
            errorEl.textContent = data.message || 'Enter a valid email.';
        } catch(e) {
            errorEl.textContent = 'Network error or invalid email.';
        }
        errorEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Add Member';
      }
    }
  };
  xhr.send('email=' + encodeURIComponent(email));
}

document.addEventListener('DOMContentLoaded', function () {
  var input = document.getElementById('newMemberEmail');
  if (input) {
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') addMember();
    });
  }
});

function removeMember(memberId, btnElement) {
  if (!confirm('Are you sure you want to remove this member? They will lose access to all projects in this workspace.')) {
    return;
  }

  var tr = btnElement.closest('tr');
  var originalText = btnElement.innerText;
  btnElement.innerText = '...';
  btnElement.disabled = true;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= BASE_URL ?>/index.php?page=api.members.delete&id=' + memberId, true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          if (data.ok) {
            tr.classList.add('fading');
            setTimeout(function() { tr.remove(); }, 400);
          } else {
            alert(data.message || 'Error removing member.');
            btnElement.innerText = originalText;
            btnElement.disabled = false;
          }
        } catch (e) {
          alert('Invalid response from server.');
          btnElement.innerText = originalText;
          btnElement.disabled = false;
        }
      } else {
        alert('Network error occurred.');
        btnElement.innerText = originalText;
        btnElement.disabled = false;
      }
    }
  }
  xhr.send();
}
</script>

</main>
  </div><!-- /.main-content -->
</div><!-- /.app-shell -->
</body>
</html>
