import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

export default createCrudPage({
  endpoint:    '/ciudades',
  title:       'Ciudades',
  singular:    'ciudad',
  formColumns: 2,
  columns: [
    { key: 'id',         label: 'ID'          },
    { key: 'nombre',     label: 'Nombre'      },
    { key: 'cod_postal', label: 'Cód. Postal' },
    { key: 'estado_id',  label: 'ID Estado'   },
  ],
  fields: [
    { key: 'nombre',     label: 'Nombre',       type: 'text',   required: true },
    { key: 'cod_postal', label: 'Código Postal', type: 'text',   required: true },
    { key: 'estado_id',  label: 'Estado',        type: 'select', required: true },
  ],
  loadRelated: async () => {
    const res = await api.get('/estados');
    return {
      estado_id: (res.data ?? []).map(e => ({ value: e.id, label: e.nombre })),
    };
  },
});
