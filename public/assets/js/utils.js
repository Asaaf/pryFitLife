/**
 * Utilidades compartidas para la SPA de FitLife.
 */

/** Escapa texto para insertarlo de forma segura como HTML. */
export function esc(value) {
  const d = document.createElement('div');
  d.textContent = value ?? '';
  return d.innerHTML;
}

/** Formatea una fecha ISO (YYYY-MM-DD) al formato local es-CO. */
export function formatDate(value) {
  if (!value) return '—';
  // Agrega zona horaria para evitar desfases por UTC.
  const d = new Date(value.includes('T') ? value : value + 'T00:00:00');
  return d.toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

/** Formatea un número como moneda COP. */
export function formatCurrency(value) {
  if (value == null || value === '') return '—';
  return new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 0,
  }).format(Number(value));
}

/** Muestra un toast de notificación. type: 'success' | 'error' | 'warning' | 'info' */
export function showToast(message, type = 'success') {
  const icons = { success: 'check-circle-fill', error: 'x-circle-fill', warning: 'exclamation-triangle-fill', info: 'info-circle-fill' };
  const icon  = icons[type] ?? 'info-circle-fill';

  const container = document.getElementById('toastContainer');
  if (!container) return;

  const el = document.createElement('div');
  el.className = `toast toast--${type}`;
  el.innerHTML = `<i class="bi bi-${icon}"></i><span>${esc(message)}</span>`;
  container.appendChild(el);

  requestAnimationFrame(() => el.classList.add('toast--show'));

  setTimeout(() => {
    el.classList.remove('toast--show');
    setTimeout(() => el.remove(), 350);
  }, 3500);
}

/** Actualiza el título de la topbar y el document title. */
export function setPageTitle(title) {
  const el = document.getElementById('pageTitle');
  if (el) el.textContent = title;
  document.title = `${title} — FitLife`;
}

/** Marca el nav-item activo según el path actual. */
export function updateActiveNav(path) {
  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.toggle('active', el.dataset.path === path);
  });
}
