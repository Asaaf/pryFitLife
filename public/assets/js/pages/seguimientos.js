import { createCrudPage } from './crud.js';
import { api }            from '../api.js';
import { formatDate }     from '../utils.js';

export default createCrudPage({
  endpoint:    '/seguimientos',
  title:       'Seguimientos',
  singular:    'seguimiento',
  formColumns: 2,
  columns: [
    { key: 'id',          label: 'ID'         },
    { key: 'id_afiliado', label: 'ID Afiliado'},
    {
      key:    'fecha',
      label:  'Fecha',
      render: s => formatDate(s.fecha),
    },
    { key: 'peso',   label: 'Peso (kg)' },
    { key: 'altura', label: 'Altura (m)'},
    { key: 'imc',    label: 'IMC'       },
  ],
  fields: [
    { key: 'id_afiliado', label: 'Afiliado',     type: 'select', required: true },
    { key: 'fecha',       label: 'Fecha',         type: 'date',   required: true },
    { key: 'peso',        label: 'Peso (kg)',     type: 'number', required: true },
    { key: 'altura',      label: 'Altura (m)',    type: 'number', required: true },
    { key: 'imc',         label: 'IMC',           type: 'number', required: true },
  ],
  loadRelated: async () => {
    const res = await api.get('/afiliados');
    return {
      id_afiliado: (res.data ?? []).map(a => ({
        value: a.id,
        label: `${a.primer_nombre ?? ''} ${a.primer_apellido ?? ''} (${a.identificacion ?? ''})`.trim(),
      })),
    };
  },
});
