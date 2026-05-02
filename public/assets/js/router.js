/**
 * Router hash-based para la SPA de FitLife.
 * Uso:
 *   router.register('/ruta', async (path) => { ... return cleanupFn; });
 *   router.init();
 *   router.navigate('/ruta');
 */

const routes = new Map();
let currentCleanup = null;

async function handle() {
  if (typeof currentCleanup === 'function') {
    currentCleanup();
    currentCleanup = null;
  }

  const path    = window.location.hash.slice(1) || '/';
  const handler = routes.get(path) ?? routes.get('*');

  if (handler) {
    const cleanup = await handler(path);
    if (typeof cleanup === 'function') currentCleanup = cleanup;
  }
}

export const router = {
  register(path, handler) {
    routes.set(path, handler);
  },

  navigate(path) {
    window.location.hash = path;
  },

  init() {
    window.addEventListener('hashchange', handle);
    handle();
  },
};
