import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

export default createCrudPage({
  endpoint:    '/ejercicios-rutina',
  title:       'Ejercicios × Rutina',
  singular:    'asignación',
  formColumns: 2,
  columns: [
    { key: 'id',           label: 'ID'           },
    { key: 'id_ejercicio', label: 'ID Ejercicio' },
    { key: 'id_rutina',    label: 'ID Rutina'    },
    { key: 'ciclos',       label: 'Ciclos'       },
    { key: 'repeticiones', label: 'Repeticiones' },
  ],
  fields: [
    { key: 'id_ejercicio', label: 'Ejercicio',    type: 'select', required: true },
    { key: 'id_rutina',    label: 'Rutina',       type: 'select', required: true },
    { key: 'ciclos',       label: 'Ciclos',       type: 'number', required: true },
    { key: 'repeticiones', label: 'Repeticiones', type: 'number', required: true },
  ],
  loadRelated: async () => {
    const [ejerciciosRes, rutinasRes] = await Promise.all([
      api.get('/ejercicios'),
      api.get('/rutinas'),
    ]);
    return {
      id_ejercicio: (ejerciciosRes.data ?? []).map(e => ({ value: e.id, label: e.nombre })),
      id_rutina:    (rutinasRes.data    ?? []).map(r => ({ value: r.id, label: r.nombre })),
    };
  },
});
