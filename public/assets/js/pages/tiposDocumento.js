import { createCrudPage } from './crud.js';

export default createCrudPage({
  endpoint:    '/tipos-documento',
  title:       'Tipos de Documento',
  singular:    'tipo de documento',
  formColumns: 2,
  columns: [
    { key: 'id',             label: 'ID'    },
    { key: 'tipo_documento', label: 'Tipo'  },
    { key: 'sigla',          label: 'Sigla' },
  ],
  fields: [
    { key: 'tipo_documento', label: 'Tipo de Documento', type: 'text', required: true  },
    { key: 'sigla',          label: 'Sigla',             type: 'text', required: false },
  ],
});
