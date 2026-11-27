import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';

/* Theme (light / dark) is applied by a tiny inline script in <head>; this wires the toggle. */
const root = document.documentElement;
const THEME_KEY = 'admin-theme';

function setTheme(theme) {
    root.setAttribute('data-bs-theme', theme);
    try { localStorage.setItem(THEME_KEY, theme); } catch (e) { /* private mode */ }
    document.querySelectorAll('[data-theme-toggle] i').forEach((i) => {
        i.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    });
}

document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => setTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark'));
});
setTheme(root.getAttribute('data-bs-theme') || 'light');

/* Mobile sidebar */
const sidebar = document.getElementById('adSidebar');
const scrim = document.getElementById('adScrim');
const toggleSidebar = (open) => {
    sidebar?.classList.toggle('open', open);
    scrim?.classList.toggle('show', open);
};
document.querySelectorAll('[data-sidebar-toggle]').forEach((b) => b.addEventListener('click', () => toggleSidebar(!sidebar.classList.contains('open'))));
scrim?.addEventListener('click', () => toggleSidebar(false));

/* Confirmation dialogs: <form data-confirm="Delete this?"> or <button data-confirm> */
document.addEventListener('submit', (event) => {
    const form = event.target;
    const message = form.dataset?.confirm;

    if (!message || form.dataset.confirmed) return;

    event.preventDefault();
    Swal.fire({
        title: form.dataset.confirmTitle || 'Are you sure?',
        text: message,
        icon: form.dataset.confirmIcon || 'warning',
        showCancelButton: true,
        confirmButtonText: form.dataset.confirmButton || 'Yes, continue',
        confirmButtonColor: form.dataset.confirmColor || '#c0392b',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.requestSubmit();
        }
    });
});

/* Disable submit buttons after the first click so a slow save is not sent twice */
document.querySelectorAll('form[data-once]').forEach((form) => {
    form.addEventListener('submit', () => {
        form.querySelectorAll('[type=submit]').forEach((b) => {
            b.disabled = true;
            b.insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>');
        });
    });
});

/* Password show / hide */
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.querySelector(btn.dataset.togglePassword);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.querySelector('i').className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    });
});

/* Live image preview for file inputs */
document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
        const target = document.querySelector(input.dataset.preview);
        const file = input.files?.[0];
        if (target && file) target.src = URL.createObjectURL(file);
    });
});

/* Live icon preview */
document.querySelectorAll('input[data-icon-preview]').forEach((input) => {
    const target = document.querySelector(input.dataset.iconPreview);
    const update = () => { if (target) target.className = 'fa-solid ' + (/^fa-[a-z0-9-]+$/.test(input.value) ? input.value : 'fa-question'); };
    input.addEventListener('input', update);
    update();
});

/* Permission matrix: row / column "select all", and create/update/delete imply view */
document.querySelectorAll('[data-matrix]').forEach((matrix) => {
    const boxes = () => [...matrix.querySelectorAll('input[data-perm]:not(:disabled)')];

    const syncHeads = () => {
        matrix.querySelectorAll('[data-col]').forEach((head) => {
            const col = boxes().filter((b) => b.dataset.action === head.dataset.col);
            head.checked = col.length > 0 && col.every((b) => b.checked);
        });
        matrix.querySelectorAll('[data-row]').forEach((head) => {
            const row = boxes().filter((b) => b.dataset.module === head.dataset.row);
            head.checked = row.length > 0 && row.every((b) => b.checked);
        });
        const count = boxes().filter((b) => b.checked).length;
        document.querySelectorAll('[data-perm-count]').forEach((el) => { el.textContent = count; });
    };

    matrix.addEventListener('change', (e) => {
        const t = e.target;

        if (t.matches('input[data-perm]')) {
            const view = matrix.querySelector(`input[data-perm][data-module="${t.dataset.module}"][data-action="view"]`);
            if (t.checked && t.dataset.action !== 'view' && view && !view.disabled) view.checked = true;
            if (!t.checked && t.dataset.action === 'view') {
                matrix.querySelectorAll(`input[data-perm][data-module="${t.dataset.module}"]`).forEach((b) => { b.checked = false; });
            }
        } else if (t.matches('[data-col]')) {
            boxes().filter((b) => b.dataset.action === t.dataset.col).forEach((b) => { b.checked = t.checked; });
        } else if (t.matches('[data-row]')) {
            boxes().filter((b) => b.dataset.module === t.dataset.row).forEach((b) => { b.checked = t.checked; });
        } else if (t.matches('[data-all]')) {
            boxes().forEach((b) => { b.checked = t.checked; });
        }
        syncHeads();
    });
    syncHeads();
});

/* Live preview: device switcher + design switcher for every <div data-preview-root> */
document.querySelectorAll('[data-preview-root]').forEach((box) => {
    const frame = box.querySelector('iframe');
    const bar = box.querySelector('[data-devices]');
    const select = box.querySelector('[data-preview-page]');
    const reload = box.querySelector('[data-preview-reload]');

    const load = () => { if (frame && select) frame.src = select.value + (select.value.includes('?') ? '&' : '?') + 'adminPreview=' + Date.now(); };

    bar?.querySelectorAll('button').forEach((b) => b.addEventListener('click', () => {
        bar.querySelectorAll('button').forEach((x) => x.classList.toggle('active', x === b));
        frame.dataset.device = b.dataset.device;
    }));
    select?.addEventListener('change', load);
    reload?.addEventListener('click', load);

    // The off-canvas preview only loads the site when it is opened.
    const canvas = box.closest('.offcanvas');
    if (canvas) canvas.addEventListener('show.bs.offcanvas', load, { once: true });
    else load();
});

/* Auto-dismiss success alerts */
document.querySelectorAll('.alert[data-autohide]').forEach((el) => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(el).close(), 5000);
});

/* Bootstrap tooltips */
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
