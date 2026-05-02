/**
 * Cliente REST para la FitLife API.
 * Todas las funciones retornan Promise<{ ok: boolean, data: any, error: string|null }>.
 */

const BASE = window.location.origin;

async function request(method, path, body = null) {
  const opts = { method, headers: { 'Content-Type': 'application/json; charset=utf-8' } };
  if (body !== null) opts.body = JSON.stringify(body);

  try {
    const res  = await fetch(BASE + path, opts);
    const json = await res.json();

    if (!res.ok) {
      const msg = json.message ?? json.errors?.[0] ?? `Error ${res.status}`;
      return { ok: false, data: null, meta: null, error: msg };
    }

    return { ok: true, data: json.data ?? json, meta: json.meta ?? null, error: null };
  } catch (err) {
    return { ok: false, data: null, meta: null, error: err.message ?? 'Error de red' };
  }
}

export const api = {
  get:    (path)           => request('GET',    path),
  post:   (path, body)     => request('POST',   path, body),
  put:    (path, id, body) => request('PUT',    `${path}/${id}`, body),
  delete: (path, id)       => request('DELETE', `${path}/${id}`),
};
