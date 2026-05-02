/**
 * Punto de entrada de la SPA FitLife.
 * Registra todas las rutas y arranca el router.
 */

import { router }                    from './router.js';
import { setPageTitle, updateActiveNav } from './utils.js';
import * as dashboard                from './pages/dashboard.js';
import paisesPage                    from './pages/paises.js';
import estadosPage                   from './pages/estados.js';
import ciudadesPage                  from './pages/ciudades.js';
import sedesPage                     from './pages/sedes.js';
import especialidadesPage            from './pages/especialidades.js';
import tiposDocumentoPage            from './pages/tiposDocumento.js';
import empleadosPage                 from './pages/empleados.js';
import planesNutricionalesPage       from './pages/planesNutricionales.js';
import rutinasPage                   from './pages/rutinas.js';
import ejerciciosPage                from './pages/ejercicios.js';
import ejerciciosRutinaPage          from './pages/ejerciciosRutina.js';
import clasesGrupalesPage            from './pages/clasesGrupales.js';
import horariosPage                  from './pages/horarios.js';
import afiliadosPage                 from './pages/afiliados.js';
import planesPage                    from './pages/planes.js';
import pagosPage                     from './pages/pagos.js';
import seguimientosPage              from './pages/seguimientos.js';

// ── Tabla de rutas ──────────────────────────────────────────────────────────

const ROUTES = [
  { path: '/',                    title: 'Dashboard',              page: dashboard              },
  { path: '/afiliados',           title: 'Afiliados',              page: afiliadosPage          },
  { path: '/seguimientos',        title: 'Seguimientos',           page: seguimientosPage       },
  { path: '/empleados',           title: 'Empleados',              page: empleadosPage          },
  { path: '/especialidades',      title: 'Especialidades',         page: especialidadesPage     },
  { path: '/rutinas',             title: 'Rutinas',                page: rutinasPage            },
  { path: '/ejercicios',          title: 'Ejercicios',             page: ejerciciosPage         },
  { path: '/ejercicios-rutina',   title: 'Ejercicios × Rutina',    page: ejerciciosRutinaPage   },
  { path: '/clases-grupales',     title: 'Clases Grupales',        page: clasesGrupalesPage     },
  { path: '/horarios',            title: 'Horarios',               page: horariosPage           },
  { path: '/planes-nutricionales',title: 'Planes Nutricionales',   page: planesNutricionalesPage},
  { path: '/planes',              title: 'Planes',                 page: planesPage             },
  { path: '/pagos',               title: 'Pagos',                  page: pagosPage              },
  { path: '/sedes',               title: 'Sedes',                  page: sedesPage              },
  { path: '/ciudades',            title: 'Ciudades',               page: ciudadesPage           },
  { path: '/estados',             title: 'Estados',                page: estadosPage            },
  { path: '/paises',              title: 'Países',                 page: paisesPage             },
  { path: '/tipos-documento',     title: 'Tipos de Documento',     page: tiposDocumentoPage     },
];

// ── Helper: monta una página genérica ────────────────────────────────────────

function mountPage(route) {
  return async () => {
    setPageTitle(route.title);
    updateActiveNav(route.path);
    document.getElementById('app').innerHTML = route.page.template;
    await route.page.init();
  };
}

// ── Registrar rutas ───────────────────────────────────────────────────────────

ROUTES.forEach(route => router.register(route.path, mountPage(route)));

// Ruta 404
router.register('*', async () => {
  setPageTitle('Página no encontrada');
  updateActiveNav('');
  document.getElementById('app').innerHTML = `
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:300px;gap:var(--s4);text-align:center">
      <i class="bi bi-exclamation-triangle-fill" style="font-size:3rem;color:var(--c-warning)"></i>
      <h2 style="font-size:1.25rem;font-weight:700">Página no encontrada</h2>
      <p style="color:var(--c-text-muted)">La ruta solicitada no existe.</p>
      <a href="#/" class="btn btn--primary">Ir al Dashboard</a>
    </div>`;
});

// ── Sidebar toggle ────────────────────────────────────────────────────────────

const shell   = document.getElementById('shell');
const sidebar = document.getElementById('sidebar');

document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  const isMobile = window.innerWidth <= 768;
  if (isMobile) {
    sidebar.classList.toggle('mobile--open');
  } else {
    shell.classList.toggle('sidebar--collapsed');
  }
});

// Cierra el sidebar móvil al hacer clic fuera.
document.addEventListener('click', e => {
  if (window.innerWidth <= 768
    && sidebar.classList.contains('mobile--open')
    && !sidebar.contains(e.target)
    && e.target.id !== 'sidebarToggle') {
    sidebar.classList.remove('mobile--open');
  }
});

// Cierra sidebar móvil al navegar.
window.addEventListener('hashchange', () => {
  if (window.innerWidth <= 768) sidebar.classList.remove('mobile--open');
});

// ── Arrancar ─────────────────────────────────────────────────────────────────

router.init();
