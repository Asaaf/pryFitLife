import { createCrudPage } from './crud.js';

export default createCrudPage({
  endpoint:    '/rutinas',
  title:       'Rutinas',
  singular:    'rutina',
  formColumns: 1,
  columns: [
    { key: 'id',          label: 'ID'          },
    { key: 'nombre',      label: 'Nombre'      },
    { key: 'descripcion', label: 'Descripción' },
  ],
  fields: [
    { key: 'nombre',      label: 'Nombre',      type: 'text',     required: true },
    { key: 'descripcion', label: 'Descripción', type: 'textarea', required: true },
  ],
});
