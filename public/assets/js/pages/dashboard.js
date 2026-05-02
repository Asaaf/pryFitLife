/**
 * Dashboard: tarjetas de estadísticas + acceso rápido a módulos.
 */

import { api }             from '../api.js';
import { formatCurrency }  from '../utils.js';

const STATS = [
  { key: 'afiliados',   label: 'Afiliados',         icon: 'people-fill',        color: 'red'    },
  { key: 'empleados',   label: 'Empleados',          icon: 'person-badge-fill',  color: 'blue'   },
  { key: 'sedes',       label: 'Sedes',              icon: 'building-fill',      color: 'green'  },
  { key: 'pagos',       label: 'Pagos registrados',  icon: 'cash-stack',         color: 'orange' },
  { key: 'rutinas',     label: 'Rutinas',            icon: 'journal-richtext',   color: 'purple' },
  { key: 'ejercicios',  label: 'Ejercicios',         icon: 'bicycle',            color: 'blue'   },
];

const QUICK_LINKS = [
  { path: '/afiliados',   label: 'Gestionar Afiliados',   icon: 'people-fill'        },
  { path: '/empleados',   label: 'Gestionar Empleados',   icon: 'person-badge-fill'  },
  { path: '/pagos',       label: 'Registrar Pago',        icon: 'cash-stack'         },
  { path: '/seguimientos',label: 'Ver Seguimientos',      icon: 'activity'           },
  { path: '/horarios',    label: 'Gestionar Horarios',    icon: 'calendar-week-fill' },
  { path: '/docs',        label: 'Documentación API',     icon: 'book-fill'          },
];

export const template = `
  <div class="page-header">
    <div>
      <h1 class="page-title">Dashboard</h1>
      <p class="page-subtitle">Resumen general del gimnasio FitLife</p>
    </div>
  </div>

  <div class="stats-grid" id="statsGrid">
    ${STATS.map(s => `
      <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--${s.color}">
          <i class="bi bi-${s.icon}"></i>
        </div>
        <div class="stat-card__value" id="stat-${s.key}">
          <div class="spinner spinner--sm"></div>
        </div>
        <div class="stat-card__label">${s.label}</div>
      </div>`).join('')}
  </div>

  <div class="page-header" style="margin-bottom:var(--s4)">
    <h2 style="font-size:1rem;font-weight:700">Acceso rápido</h2>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:var(--s4);margin-bottom:var(--s8)">
    ${QUICK_LINKS.map(l => {
      const href = l.path.startsWith('/docs')
        ? '/docs'
        : `#${l.path}`;
      const target = l.path.startsWith('/docs') ? ' target="_blank"' : '';
      return `<a href="${href}" class="stat-card"${target} style="cursor:pointer;text-decoration:none;align-items:flex-start;gap:var(--s3)">
        <i class="bi bi-${l.icon}" style="font-size:1.4rem;color:var(--c-primary)"></i>
        <span style="font-size:.875rem;font-weight:600">${l.label}</span>
      </a>`;
    }).join('')}
  </div>`;

export async function init() {
  // Carga todos los conteos en paralelo.
  const results = await Promise.all(
    STATS.map(s => api.get(`/${s.key}`).then(r => ({ key: s.key, count: Array.isArray(r.data) ? r.data.length : '—' }))),
  );

  results.forEach(({ key, count }) => {
    const el = document.getElementById(`stat-${key}`);
    if (el) el.textContent = count;
  });
}
