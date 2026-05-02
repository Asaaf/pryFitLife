import { createCrudPage }  from './crud.js';
import { api }             from '../api.js';
import { formatDate, formatCurrency } from '../utils.js';

export default createCrudPage({
  endpoint:    '/empleados',
  title:       'Empleados',
  singular:    'empleado',
  formColumns: 2,
  columns: [
    { key: 'id',             label: 'ID'            },
    { key: 'identificacion', label: 'Identificación'},
    {
      key:    'primer_nombre',
      label:  'Nombre completo',
      render: e => `${e.primer_nombre ?? ''} ${e.primer_apellido ?? ''}`.trim() || '—',
    },
    {
      key:    'salario',
      label:  'Salario',
      render: e => formatCurrency(e.salario),
    },
    {
      key:    'fecha_ingreso',
      label:  'Ingreso',
      render: e => formatDate(e.fecha_ingreso),
    },
  ],
  fields: [
    { key: 'identificacion',    label: 'Identificación',    type: 'text',   required: true  },
    { key: 'tipo_documento_id', label: 'Tipo Documento',    type: 'select', required: true  },
    { key: 'primer_nombre',     label: 'Primer nombre',     type: 'text',   required: true  },
    { key: 'segundo_nombre',    label: 'Segundo nombre',    type: 'text',   required: false },
    { key: 'primer_apellido',   label: 'Primer apellido',   type: 'text',   required: true  },
    { key: 'segundo_apellido',  label: 'Segundo apellido',  type: 'text',   required: false },
    { key: 'salario',           label: 'Salario (COP)',     type: 'number', required: true  },
    { key: 'fecha_ingreso',     label: 'Fecha de ingreso',  type: 'date',   required: true  },
    { key: 'sede_id',           label: 'Sede',              type: 'select', required: true  },
    { key: 'especialidad_id',   label: 'Especialidad',      type: 'select', required: true  },
  ],
  loadRelated: async () => {
    const [tiposRes, sedesRes, espRes] = await Promise.all([
      api.get('/tipos-documento'),
      api.get('/sedes'),
      api.get('/especialidades'),
    ]);
    return {
      tipo_documento_id: (tiposRes.data ?? []).map(t => ({ value: t.id, label: `${t.sigla ?? ''} – ${t.tipo_documento}` })),
      sede_id:           (sedesRes.data  ?? []).map(s => ({ value: s.id, label: s.direccion })),
      especialidad_id:   (espRes.data    ?? []).map(e => ({ value: e.id, label: e.nombre })),
    };
  },
});
