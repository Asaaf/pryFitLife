/**
 * Factory CRUD genérico.
 *
 * Uso:
 *   import { createCrudPage } from './crud.js';
 *   export default createCrudPage({ endpoint, title, singular, columns, fields, formColumns?, loadRelated?, onModalReady? });
 *
 * config.columns  = [{ key, label, render? }]
 * config.fields   = [{ key, label, type, required?, options?, span2? }]
 *   type: 'text' | 'number' | 'date' | 'time' | 'email' | 'textarea' | 'select'
 *   Para selects dinámicos: { key, type: 'select' } sin options → se rellenan via loadRelated
 * config.formColumns  = 1 | 2  (default 2)
 * config.loadRelated  = async () => ({ fieldKey: [{ value, label }] })
 * config.onModalReady = async ({ item, related, fields, setOptions, setValue, getValue }) => void
 */

import { api }       from '../api.js';
import { esc, showToast } from '../utils.js';

export function createCrudPage(config) {
  const { endpoint, title, singular, columns, fields } = config;
  const formColumns   = config.formColumns ?? 2;
  const pageSizeOptions = [10, 20, 50, 100];

  // ── RENDERING HELPERS ──────────────────────────────────────────────────────

  function cellValue(col, item) {
    if (col.render) return col.render(item);
    const v = item[col.key];
    return v != null && v !== '' ? esc(String(v)) : '<span style="color:var(--c-text-muted)">—</span>';
  }

  function buildRows(data) {
    if (!data.length) {
      return `<tr><td colspan="${columns.length + 1}" class="empty">Sin registros</td></tr>`;
    }
    return data.map(item => `
      <tr data-id="${item.id}">
        ${columns.map(col => `<td>${cellValue(col, item)}</td>`).join('')}
        <td class="actions">
          <button class="btn-icon btn-icon--edit"   data-action="edit"   data-id="${item.id}" title="Editar">
            <i class="bi bi-pencil-fill"></i>
          </button>
          <button class="btn-icon btn-icon--delete" data-action="delete" data-id="${item.id}" title="Eliminar">
            <i class="bi bi-trash-fill"></i>
          </button>
        </td>
      </tr>`).join('');
  }

  function buildField(f, value, resolvedOptions) {
    const id  = `field_${f.key}`;
    const req = f.required ? 'required' : '';
    const spanClass = f.span2 ? 'form-group span-2' : 'form-group';

    if (f.type === 'textarea') {
      return `<div class="${spanClass}">
        <label for="${id}">${esc(f.label)}</label>
        <textarea id="${id}" name="${f.key}" ${req} rows="3">${esc(value)}</textarea>
      </div>`;
    }

    if (f.type === 'select') {
      const opts = (resolvedOptions ?? f.options ?? [])
        .map(o => `<option value="${esc(String(o.value))}" ${String(value) === String(o.value) ? 'selected' : ''}>${esc(o.label)}</option>`)
        .join('');
      return `<div class="${spanClass}">
        <label for="${id}">${esc(f.label)}</label>
        <select id="${id}" name="${f.key}" ${req}>
          <option value="">— Seleccionar —</option>
          ${opts}
        </select>
      </div>`;
    }

    return `<div class="${spanClass}">
      <label for="${id}">${esc(f.label)}</label>
      <input id="${id}" type="${f.type ?? 'text'}" name="${f.key}" value="${esc(value)}" ${req} />
    </div>`;
  }

  function buildForm(item, related) {
    const gridClass = `form-grid${formColumns === 1 ? ' form-grid--1col' : ''}`;
    const html = fields.map(f => {
      const value   = item ? (item[f.key] ?? '') : '';
      const options = related?.[f.key] ?? null;
      return buildField(f, value, options);
    }).join('');
    return `<div class="${gridClass}">${html}</div>`;
  }

  function readForm() {
    const data = {};
    fields.forEach(f => {
      const el = document.getElementById(`field_${f.key}`);
      if (el) data[f.key] = el.value;
    });
    return data;
  }

  function setSelectOptions(fieldKey, options, selectedValue = '') {
    const select = document.getElementById(`field_${fieldKey}`);
    if (!select) return;

    select.innerHTML = [
      '<option value="">— Seleccionar —</option>',
      ...(options ?? []).map(o => `<option value="${esc(String(o.value))}" ${String(selectedValue) === String(o.value) ? 'selected' : ''}>${esc(o.label)}</option>`),
    ].join('');
  }

  function setFieldValue(fieldKey, value) {
    const field = document.getElementById(`field_${fieldKey}`);
    if (field) {
      field.value = value ?? '';
    }
  }

  function getFieldValue(fieldKey) {
    const field = document.getElementById(`field_${fieldKey}`);
    return field ? field.value : '';
  }

  // ── MODAL HELPERS ──────────────────────────────────────────────────────────

  function openModal() {
    document.getElementById('modal').classList.add('modal--open');
    const firstInput = document.querySelector('#modalBody input, #modalBody select, #modalBody textarea');
    if (firstInput) firstInput.focus();
  }

  function closeModal() {
    document.getElementById('modal').classList.remove('modal--open');
  }

  // ── PAGE TEMPLATE ─────────────────────────────────────────────────────────

  const headerRow = [
    ...columns.map(c => `<th>${esc(c.label)}</th>`),
    '<th class="th-actions">Acciones</th>',
  ].join('');

  const template = `
    <div class="page-header">
      <div>
        <h1 class="page-title">${esc(title)}</h1>
      </div>
      <button class="btn btn--primary" id="btnNew">
        <i class="bi bi-plus-lg"></i> Nuevo ${esc(singular)}
      </button>
    </div>
    <div class="card">
      <div class="card-toolbar">
        <input type="search" class="input-search" id="searchInput" placeholder="Buscar en ${esc(title).toLowerCase()}..." />
        <label class="page-size-control" for="pageSizeSelect">
          <span>Registros por página</span>
          <select id="pageSizeSelect" class="input-select-inline">
            ${pageSizeOptions.map(size => `<option value="${size}" ${size === 10 ? 'selected' : ''}>${size}</option>`).join('')}
          </select>
        </label>
        <span class="topbar__api-label" id="rowCount" style="margin-left:auto;color:var(--c-text-muted);font-size:.8rem"></span>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr>${headerRow}</tr></thead>
          <tbody id="crudTbody">
            <tr><td colspan="${columns.length + 1}" class="empty">
              <div class="spinner spinner--sm"></div>
            </td></tr>
          </tbody>
        </table>
      </div>
      <div class="pagination-bar">
        <span class="pagination-bar__summary" id="paginationSummary">Mostrando 0 registros</span>
        <div class="pagination-controls">
          <button class="btn btn--ghost" id="btnPrevPage" type="button">
            <i class="bi bi-chevron-left"></i> Anterior
          </button>
          <span class="pagination-controls__status" id="pageStatus">Página 1 de 1</span>
          <button class="btn btn--ghost" id="btnNextPage" type="button">
            Siguiente <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>`;

  // ── INIT ──────────────────────────────────────────────────────────────────

  async function init() {
    let items         = [];
    let related       = {};
    let currentPage   = 1;
    let pageSize      = 10;
    let searchQuery   = '';
    let totalItems    = 0;
    let totalPages    = 1;

    function updatePagination(dataLength) {
      const summary = document.getElementById('paginationSummary');
      const status  = document.getElementById('pageStatus');
      const btnPrev = document.getElementById('btnPrevPage');
      const btnNext = document.getElementById('btnNextPage');

      const start = dataLength === 0 ? 0 : ((currentPage - 1) * pageSize) + 1;
      const end   = dataLength === 0 ? 0 : start + dataLength - 1;

      if (summary) {
        summary.textContent = dataLength === 0
          ? 'Mostrando 0 registros'
          : `Mostrando ${start}-${end} de ${totalItems} registros`;
      }

      if (status) {
        status.textContent = `Página ${totalPages === 0 ? 1 : currentPage} de ${totalPages}`;
      }

      if (btnPrev) btnPrev.disabled = currentPage <= 1 || totalItems === 0;
      if (btnNext) btnNext.disabled = currentPage >= totalPages || totalItems === 0;
    }

    // Precarga datos relacionados para el modal (una sola vez).
    if (config.loadRelated) {
      related = await config.loadRelated();
    }

    async function load() {
      const params = new URLSearchParams({
        page: String(currentPage),
        per_page: String(pageSize),
      });

      if (searchQuery) {
        params.set('q', searchQuery);
      }

      const res = await api.get(`${endpoint}?${params.toString()}`);
      if (!res.ok) { showToast(res.error, 'error'); return; }
      items = Array.isArray(res.data) ? res.data : [];
      totalItems = Number(res.meta?.total ?? items.length);
      totalPages = Math.max(1, Number(res.meta?.total_pages ?? 1));
      currentPage = Math.min(Math.max(1, Number(res.meta?.page ?? currentPage)), totalPages);
      renderTable(items);
    }

    function renderTable(data) {
      const tbody       = document.getElementById('crudTbody');
      const rowCount    = document.getElementById('rowCount');

      if (tbody) {
        tbody.innerHTML = buildRows(data);
      }

      if (rowCount) {
        rowCount.textContent = `${totalItems} registro${totalItems !== 1 ? 's' : ''}`;
      }

      updatePagination(data.length);
    }

    function prepareModal(item = null) {
      document.getElementById('modalTitle').textContent = item
        ? `Editar ${singular}`
        : `Nuevo ${singular}`;
      document.getElementById('modalBody').innerHTML = buildForm(item, related);

      if (config.onModalReady) {
        config.onModalReady({
          item,
          related,
          fields,
          setOptions: setSelectOptions,
          setValue: setFieldValue,
          getValue: getFieldValue,
        });
      }

      // Reasigna el handler de guardado para evitar acumulación de listeners.
      document.getElementById('modalSave').onclick = async () => {
        const btn  = document.getElementById('modalSave');
        const data = readForm();
        btn.disabled = true;

        const res = item
          ? await api.put(endpoint, item.id, data)
          : await api.post(endpoint, data);

        btn.disabled = false;

        if (!res.ok) { showToast(res.error, 'error'); return; }
        showToast(item ? `${singular} actualizado.` : `${singular} creado.`, 'success');
        closeModal();
        await load();
      };

      // Cierra con la X y el botón Cancelar.
      document.getElementById('modalClose').onclick  = closeModal;
      document.getElementById('modalCancel').onclick = closeModal;
      document.getElementById('modal').onclick = e => {
        if (e.target === document.getElementById('modal')) closeModal();
      };

      openModal();
    }

    async function handleDelete(id) {
      if (!window.confirm(`¿Deseas eliminar este ${singular}? Esta acción no se puede deshacer.`)) return;
      const res = await api.delete(endpoint, id);
      if (!res.ok) { showToast(res.error, 'error'); return; }
      showToast(`${singular} eliminado.`, 'success');
      await load();
    }

    // Botón "Nuevo"
    document.getElementById('btnNew')?.addEventListener('click', () => prepareModal(null));

    // Acciones dentro de la tabla (delegación en tbody).
    document.getElementById('crudTbody')?.addEventListener('click', e => {
      const btn = e.target.closest('[data-action]');
      if (!btn) return;
      const id   = btn.dataset.id;
      const item = items.find(i => String(i.id) === id);
      if (btn.dataset.action === 'edit'   && item) prepareModal(item);
      if (btn.dataset.action === 'delete' && id)   handleDelete(id);
    });

    // Búsqueda en tiempo real (filtro local).
    document.getElementById('searchInput')?.addEventListener('input', e => {
      searchQuery = e.target.value.trim();
      currentPage = 1;
      load();
    });

    document.getElementById('pageSizeSelect')?.addEventListener('change', e => {
      pageSize = Number(e.target.value) || 10;
      currentPage = 1;
      load();
    });

    document.getElementById('btnPrevPage')?.addEventListener('click', () => {
      if (currentPage <= 1) return;
      currentPage -= 1;
      load();
    });

    document.getElementById('btnNextPage')?.addEventListener('click', () => {
      if (currentPage >= totalPages) return;
      currentPage += 1;
      load();
    });

    await load();
  }

  return { template, init };
}
