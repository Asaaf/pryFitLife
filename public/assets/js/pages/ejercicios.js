import { createCrudPage } from './crud.js';

export default createCrudPage({
  endpoint:    '/ejercicios',
  title:       'Ejercicios',
  singular:    'ejercicio',
  formColumns: 2,
  columns: [
    { key: 'id',          label: 'ID'          },
    { key: 'nombre',      label: 'Nombre'      },
    { key: 'descripcion', label: 'Descripción' },
    { key: 'maquina',     label: 'Máquina'     },
  ],
  fields: [
    { key: 'nombre',      label: 'Nombre',      type: 'text',     required: true  },
    { key: 'maquina',     label: 'Máquina',     type: 'text',     required: false },
    { key: 'descripcion', label: 'Descripción', type: 'textarea', required: true, span2: true },
    { key: 'imagen',      label: 'Imagen (URL o nombre de archivo)', type: 'text', required: false, span2: true },
  ],
});
