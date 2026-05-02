import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

export default createCrudPage({
  endpoint:    '/estados',
  title:       'Estados',
  singular:    'estado',
  formColumns: 2,
  columns: [
    { key: 'id',         label: 'ID'           },
    { key: 'nombre',     label: 'Nombre'       },
    { key: 'cod_postal', label: 'Cód. Postal'  },
    { key: 'paises_id',  label: 'ID País'      },
  ],
  fields: [
    { key: 'nombre',     label: 'Nombre',       type: 'text',   required: true },
    { key: 'cod_postal', label: 'Código Postal', type: 'text',   required: true },
    { key: 'paises_id',  label: 'País',          type: 'select', required: true },
  ],
  loadRelated: async () => {
    const res = await api.get('/paises');
    return {
      paises_id: (res.data ?? []).map(p => ({ value: p.id, label: p.nombre })),
    };
  },
});
