/* mApp.js — ProjectHub Task 2: Project Management
   AJAX archive/unarchive + UI helpers */

'use strict';

/* ── 1. Auto-dismiss flash toast ── */
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('flash-toast');
    if (toast) {
        setTimeout(() => toast.remove(), 3600);
    }
});

/* ── 2. Progress bar animation on load ── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0';
        requestAnimationFrame(() => {
            setTimeout(() => { bar.style.width = target; }, 80);
        });
    });
});

/* ── 3. Archive / Unarchive via AJAX ── */
document.addEventListener('click', e => {
    const archiveBtn   = e.target.closest('.btn-archive');
    const unarchiveBtn = e.target.closest('.btn-unarchive');
    const btn = archiveBtn || unarchiveBtn;
    if (!btn) return;

    e.preventDefault();
    const id     = btn.dataset.id;
    const action = btn.dataset.action; // 'archive' or 'unarchive'
    const label  = action === 'archive' ? 'archive' : 'restore';

    showConfirm(
        action === 'archive' ? 'Archive Project?' : 'Restore Project?',
        action === 'archive'
            ? 'This project will be moved to Archived. You can restore it later.'
            : 'This project will be moved back to Active.',
        label,
        () => doArchive(id, action)
    );
});

function doArchive(id, action) {
    const url = `api/mProjects.php?id=${encodeURIComponent(id)}&action=${encodeURIComponent(action)}`;

    fetch(url, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error('Server error ' + res.status);
        return res.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Remove card from current tab view
            const card = document.getElementById(`project-card-${id}`);
            if (card) {
                card.style.transition = 'opacity .4s, transform .4s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(.95)';
                setTimeout(() => {
                    card.remove();
                    checkEmptyGrid();
                    // Reload to update counts in tabs
                    setTimeout(() => location.reload(), 800);
                }, 420);
            }
        } else {
            showToast(data.error || 'Something went wrong.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Network error. Please try again.', 'error');
    });
}

/* ── 4. Confirm Modal ── */
function showConfirm(title, message, actionLabel, onConfirm) {
    removeModal();

    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.id = 'confirm-modal';
    overlay.innerHTML = `
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <h3 id="modal-title">${escHtml(title)}</h3>
            <p>${escHtml(message)}</p>
            <div class="modal-actions">
                <button class="btn btn-ghost" id="modal-cancel">Cancel</button>
                <button class="btn btn-danger" id="modal-confirm">${escHtml(actionLabel.charAt(0).toUpperCase() + actionLabel.slice(1))}</button>
            </div>
        </div>`;

    document.body.appendChild(overlay);
    document.getElementById('modal-cancel').focus();

    document.getElementById('modal-cancel').addEventListener('click', removeModal);
    overlay.addEventListener('click', e => { if (e.target === overlay) removeModal(); });

    document.getElementById('modal-confirm').addEventListener('click', () => {
        removeModal();
        onConfirm();
    });

    // Keyboard ESC
    const onKey = e => { if (e.key === 'Escape') { removeModal(); document.removeEventListener('keydown', onKey); } };
    document.addEventListener('keydown', onKey);
}

function removeModal() {
    const m = document.getElementById('confirm-modal');
    if (m) m.remove();
}

/* ── 5. Toast Notification ── */
function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast[data-js]');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.dataset.js = '1';
    toast.innerHTML = type === 'success'
        ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> ${escHtml(message)}`
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> ${escHtml(message)}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

/* ── 6. Check empty grid after removal ── */
function checkEmptyGrid() {
    const grids = document.querySelectorAll('#active-grid, #archived-grid');
    grids.forEach(grid => {
        const cards = grid.querySelectorAll('.project-card');
        if (cards.length === 0 && !grid.querySelector('.empty-state')) {
            grid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📁</div>
                    <h3>No projects here</h3>
                    <p>Nothing to show in this tab.</p>
                </div>`;
        }
    });
}

/* ── 7. Colour swatch keyboard navigation ── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.swatch-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.swatch-radio').forEach(r => {
                r.nextElementSibling?.classList.remove('ring');
            });
        });
    });
});

/* ── 8. Client-side form pre-validation (UX only — server validates) ── */
document.addEventListener('DOMContentLoaded', () => {
    ['create-project-form', 'edit-project-form'].forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', e => {
            let valid = true;

            // Name
            const nameInput = form.querySelector('[name="name"]');
            if (nameInput && nameInput.value.trim() === '') {
                markError(nameInput, 'Project name is required.');
                valid = false;
            } else if (nameInput) clearError(nameInput);

            // Members
            const memberChecks = form.querySelectorAll('[name="members[]"]:checked');
            const memberList   = form.querySelector('#member-checkboxes');
            if (memberChecks.length === 0 && memberList) {
                const existing = memberList.parentElement.querySelector('.error-msg');
                if (!existing) {
                    const err = document.createElement('span');
                    err.className = 'error-msg';
                    err.textContent = 'At least one member must be selected.';
                    memberList.parentElement.appendChild(err);
                }
                memberList.style.borderColor = 'rgba(239,68,68,.5)';
                valid = false;
            } else if (memberList) {
                memberList.style.borderColor = '';
                const existing = memberList.parentElement.querySelector('.error-msg');
                if (existing) existing.remove();
            }

            if (!valid) e.preventDefault();
        });
    });
});

function markError(input, msg) {
    input.style.borderColor = 'rgba(239,68,68,.5)';
    let err = input.parentElement.querySelector('.error-msg');
    if (!err) {
        err = document.createElement('span');
        err.className = 'error-msg';
        input.parentElement.appendChild(err);
    }
    err.textContent = msg;
}
function clearError(input) {
    input.style.borderColor = '';
    const err = input.parentElement.querySelector('.error-msg');
    if (err) err.remove();
}

/* ── Utility ── */
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
