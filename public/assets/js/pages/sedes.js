import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

export default createCrudPage({
  endpoint:    '/sedes',
  title:       'Sedes',
  singular:    'sede',
  formColumns: 2,
  columns: [
    { key: 'id',        label: 'ID'        },
    { key: 'direccion', label: 'Dirección' },
    { key: 'telefono',  label: 'Teléfono'  },
    { key: 'ciudad_id', label: 'ID Ciudad' },
  ],
  fields: [
    { key: 'direccion', label: 'Dirección', type: 'text',   required: true  },
    { key: 'telefono',  label: 'Teléfono',  type: 'text',   required: true  },
    { key: 'ciudad_id', label: 'Ciudad',    type: 'select', required: true  },
  ],
  loadRelated: async () => {
    const res = await api.get('/ciudades');
    return {
      ciudad_id: (res.data ?? []).map(c => ({ value: c.id, label: c.nombre })),
    };
  },
});
