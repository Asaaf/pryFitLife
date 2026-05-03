import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

// Lookup maps populados en loadRelated, usados por los render de columnas
const ejercicioMap = new Map();
const rutinaMap    = new Map();

export default createCrudPage({
  endpoint:    '/ejercicios-rutina',
  title:       'Ejercicios × Rutina',
  singular:    'asignación',
  formColumns: 2,
  columns: [
    { key: 'id',           label: 'ID'           },
    { key: 'id_ejercicio', label: 'Ejercicio',    render: item => ejercicioMap.get(item.id_ejercicio) ?? item.id_ejercicio },
    { key: 'id_rutina',    label: 'Rutina',       render: item => rutinaMap.get(item.id_rutina)       ?? item.id_rutina    },
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
    const ejercicios = ejerciciosRes.data ?? [];
    const rutinas    = rutinasRes.data    ?? [];

    ejercicios.forEach(e => ejercicioMap.set(e.id, e.nombre));
    rutinas.forEach(r => rutinaMap.set(r.id, r.nombre));

    return {
      id_ejercicio: ejercicios.map(e => ({ value: e.id, label: e.nombre })),
      id_rutina:    rutinas.map(r => ({ value: r.id, label: r.nombre })),
    };
  },
});
