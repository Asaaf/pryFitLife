import { createCrudPage } from './crud.js';
import { api }            from '../api.js';
import { formatDate }     from '../utils.js';

export default createCrudPage({
  endpoint:    '/horarios',
  title:       'Horarios',
  singular:    'horario',
  formColumns: 2,
  columns: [
    { key: 'id',              label: 'ID'               },
    { key: 'id_clase_grupal', label: 'ID Clase'         },
    { key: 'id_empleado',     label: 'ID Empleado'      },
    {
      key:    'fecha_inicio',
      label:  'Fecha inicio',
      render: h => formatDate(h.fecha_inicio),
    },
    {
      key:    'fecha_fin',
      label:  'Fecha fin',
      render: h => formatDate(h.fecha_fin),
    },
    { key: 'hora_inicio', label: 'Hora inicio' },
    { key: 'hora_fin',    label: 'Hora fin'    },
  ],
  fields: [
    { key: 'id_clase_grupal', label: 'Clase Grupal',   type: 'select', required: true },
    { key: 'id_empleado',     label: 'Empleado',       type: 'select', required: true },
    { key: 'fecha_inicio',    label: 'Fecha inicio',   type: 'date',   required: true },
    { key: 'fecha_fin',       label: 'Fecha fin',      type: 'date',   required: true },
    { key: 'hora_inicio',     label: 'Hora inicio',    type: 'time',   required: true },
    { key: 'hora_fin',        label: 'Hora fin',       type: 'time',   required: true },
  ],
  loadRelated: async () => {
    const [clasesRes, empleadosRes] = await Promise.all([
      api.get('/clases-grupales'),
      api.get('/empleados'),
    ]);
    return {
      id_clase_grupal: (clasesRes.data    ?? []).map(c => ({ value: c.id, label: `${c.nombre} (${c.intensidad})` })),
      id_empleado:     (empleadosRes.data ?? []).map(e => ({
        value: e.id,
        label: `${e.primer_nombre ?? ''} ${e.primer_apellido ?? ''}`.trim() || `Empleado #${e.id}`,
      })),
    };
  },
});
