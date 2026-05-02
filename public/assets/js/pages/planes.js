import { createCrudPage } from './crud.js';
import { formatCurrency }  from '../utils.js';

export default createCrudPage({
  endpoint:    '/planes',
  title:       'Planes',
  singular:    'plan',
  formColumns: 2,
  columns: [
    { key: 'id',          label: 'ID'           },
    { key: 'nombre',      label: 'Nombre'       },
    { key: 'descripcion', label: 'Descripción'  },
    {
      key:    'valor',
      label:  'Valor',
      render: item => `<strong>${formatCurrency(item.valor)}</strong>`,
    },
  ],
  fields: [
    { key: 'nombre',      label: 'Nombre',      type: 'text',     required: true  },
    { key: 'valor',       label: 'Valor (COP)', type: 'number',   required: true  },
    { key: 'descripcion', label: 'Descripción', type: 'textarea', required: false, span2: true },
  ],
});
