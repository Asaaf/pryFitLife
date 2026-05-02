import { createCrudPage } from './crud.js';
import { api }            from '../api.js';
import { formatDate }     from '../utils.js';

export default createCrudPage({
  endpoint:    '/afiliados',
  title:       'Afiliados',
  singular:    'afiliado',
  formColumns: 2,
  columns: [
    { key: 'id',                 label: 'ID'            },
    { key: 'identificacion',     label: 'Identificación'},
    {
      key:    'primer_nombre',
      label:  'Nombre completo',
      render: a => `${a.primer_nombre ?? ''} ${a.primer_apellido ?? ''}`.trim() || '—',
    },
    { key: 'correo_electronico', label: 'Correo'        },
    {
      key:    'fecha_nacimiento',
      label:  'Nacimiento',
      render: a => formatDate(a.fecha_nacimiento),
    },
  ],
  fields: [
    { key: 'identificacion',      label: 'Identificación',    type: 'text',   required: true  },
    { key: 'id_tipo_documento',   label: 'Tipo Documento',    type: 'select', required: true  },
    { key: 'primer_nombre',       label: 'Primer nombre',     type: 'text',   required: true  },
    { key: 'segundo_nombre',      label: 'Segundo nombre',    type: 'text',   required: false },
    { key: 'primer_apellido',     label: 'Primer apellido',   type: 'text',   required: true  },
    { key: 'segundo_apellido',    label: 'Segundo apellido',  type: 'text',   required: false },
    { key: 'correo_electronico',  label: 'Correo electrónico',type: 'email',  required: true  },
    { key: 'fecha_nacimiento',    label: 'Fecha de nacimiento',type: 'date',  required: true  },
    { key: 'id_plan_nutricional', label: 'Plan Nutricional',  type: 'select', required: true  },
    { key: 'rutina_id',           label: 'Rutina',            type: 'select', required: true  },
  ],
  loadRelated: async () => {
    const [tiposRes, planesRes, rutinasRes] = await Promise.all([
      api.get('/tipos-documento'),
      api.get('/planes-nutricionales'),
      api.get('/rutinas'),
    ]);
    return {
      id_tipo_documento:   (tiposRes.data   ?? []).map(t => ({ value: t.id, label: `${t.sigla ?? ''} – ${t.tipo_documento}` })),
      id_plan_nutricional: (planesRes.data  ?? []).map(p => ({ value: p.id, label: p.nombre })),
      rutina_id:           (rutinasRes.data ?? []).map(r => ({ value: r.id, label: r.nombre })),
    };
  },
});
