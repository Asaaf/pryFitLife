import { createCrudPage } from './crud.js';

const BADGE = {
  BAJA:  '<span class="badge badge--green">BAJA</span>',
  MEDIA: '<span class="badge badge--orange">MEDIA</span>',
  ALTA:  '<span class="badge badge--red">ALTA</span>',
};

export default createCrudPage({
  endpoint:    '/clases-grupales',
  title:       'Clases Grupales',
  singular:    'clase grupal',
  formColumns: 2,
  columns: [
    { key: 'id',         label: 'ID'          },
    { key: 'nombre',     label: 'Nombre'      },
    {
      key:    'intensidad',
      label:  'Intensidad',
      render: item => BADGE[item.intensidad] ?? `<span class="badge badge--gray">${item.intensidad ?? '—'}</span>`,
    },
  ],
  fields: [
    { key: 'nombre',     label: 'Nombre',     type: 'text',   required: true },
    {
      key:     'intensidad',
      label:   'Intensidad',
      type:    'select',
      required: true,
      options: [
        { value: 'BAJA',  label: 'Baja'  },
        { value: 'MEDIA', label: 'Media' },
        { value: 'ALTA',  label: 'Alta'  },
      ],
    },
  ],
});
