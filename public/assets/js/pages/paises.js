import { createCrudPage } from './crud.js';

export default createCrudPage({
  endpoint:    '/paises',
  title:       'Países',
  singular:    'país',
  formColumns: 2,
  columns: [
    { key: 'id',         label: 'ID'           },
    { key: 'nombre',     label: 'Nombre'       },
    { key: 'cod_postal', label: 'Cód. Postal'  },
  ],
  fields: [
    { key: 'nombre',     label: 'Nombre',       type: 'text', required: true },
    { key: 'cod_postal', label: 'Código Postal', type: 'text', required: true },
  ],
});
