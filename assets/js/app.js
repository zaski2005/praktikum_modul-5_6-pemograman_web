/**
 * Elektromedis Management System — app.js
 */

// ===== SIDEBAR TOGGLE =====
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
}

// ===== TOAST NOTIFICATION =====
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const id = 'toast-' + Date.now();
    const icons = {
        success: 'bi-check-circle-fill text-success',
        danger: 'bi-x-circle-fill text-danger',
        warning: 'bi-exclamation-triangle-fill text-warning',
        info: 'bi-info-circle-fill text-info',
    };
    const icon = icons[type] || icons.info;

    const toastHTML = `
    <div id="${id}" class="toast align-items-center border-0 shadow" role="alert" aria-atomic="true">
        <div class="d-flex align-items-center p-3 gap-2">
            <i class="bi ${icon} fs-5 flex-shrink-0"></i>
            <div class="me-auto fw-medium" style="font-size:0.875rem">${message}</div>
            <button type="button" class="btn-close btn-close-sm ms-2" data-bs-dismiss="toast"></button>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', toastHTML);
    const toastEl = document.getElementById(id);
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// ===== AUTO-SHOW TOAST FROM SESSION =====
document.addEventListener('DOMContentLoaded', function () {
    // Show flash message if exists
    const flashMsg = document.getElementById('flashMessage');
    if (flashMsg) {
        const msg = flashMsg.dataset.message;
        const type = flashMsg.dataset.type || 'success';
        if (msg) showToast(msg, type);
    }
});

// ===== SEARCH (live filter) =====
function initSearch(inputId, tableBodyId) {
    const input = document.getElementById(inputId);
    const tbody = document.getElementById(tableBodyId);
    if (!input || !tbody) return;

    // Server-side search with debounce
    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const q = input.value.trim();
            const url = new URL(window.location.href);
            if (q) url.searchParams.set('q', q);
            else url.searchParams.delete('q');
            window.location.href = url.toString();
        }, 500);
    });
}

// ===== DELETE CONFIRM =====
function confirmDelete(id, nama) {
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteForm').action = 'hapus.php?id=' + encodeURIComponent(id);
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// ===== EDIT MODAL — populate fields =====
function openEditModal(data) {
    document.getElementById('edit_id').value       = data.id;
    document.getElementById('edit_nama_alat').value = data.nama_alat;
    document.getElementById('edit_tahun').value    = data.tahun;
    document.getElementById('edit_merek').value    = data.merek;
    document.getElementById('edit_lokasi').value   = data.lokasi;

    // Reset validation state
    document.getElementById('editForm').classList.remove('was-validated');

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

// ===== FORM VALIDATION =====
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
