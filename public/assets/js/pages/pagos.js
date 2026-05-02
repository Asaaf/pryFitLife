import { createCrudPage } from './crud.js';
import { api }            from '../api.js';
import { formatDate, formatCurrency } from '../utils.js';

export default createCrudPage({
  endpoint:    '/pagos',
  title:       'Pagos',
  singular:    'pago',
  formColumns: 2,
  columns: [
    { key: 'id',           label: 'ID'         },
    { key: 'nro_recibo',   label: 'Recibo'     },
    {
      key:    'fecha_pago',
      label:  'Fecha',
      render: p => formatDate(p.fecha_pago),
    },
    {
      key:    'valor_pagado',
      label:  'Valor',
      render: p => `<strong>${formatCurrency(p.valor_pagado)}</strong>`,
    },
    { key: 'metodo_pago',  label: 'Método'     },
    { key: 'afiliado_id',  label: 'ID Afiliado'},
  ],
  fields: [
    { key: 'plan_id',      label: 'Plan',               type: 'select', required: true },
    { key: 'afiliado_id',  label: 'Afiliado',           type: 'select', required: true },
    { key: 'nro_recibo',   label: 'Número de recibo',   type: 'text',   required: true },
    { key: 'fecha_pago',   label: 'Fecha de pago',      type: 'date',   required: true },
    { key: 'valor_pagado', label: 'Valor pagado (COP)', type: 'number', required: true },
    { key: 'metodo_pago',  label: 'Método de pago',     type: 'select', required: true,
      options: [
        { value: 'Efectivo',      label: 'Efectivo'      },
        { value: 'Transferencia', label: 'Transferencia' },
        { value: 'Tarjeta',       label: 'Tarjeta'       },
        { value: 'Nequi',         label: 'Nequi'         },
        { value: 'Daviplata',     label: 'Daviplata'     },
      ],
    },
  ],
  loadRelated: async () => {
    const [planesRes, afiliadosRes] = await Promise.all([
      api.get('/planes'),
      api.get('/afiliados'),
    ]);
    return {
      plan_id:     (planesRes.data     ?? []).map(p => ({ value: p.id, label: `${p.nombre} – ${formatCurrency(p.valor)}` })),
      afiliado_id: (afiliadosRes.data  ?? []).map(a => ({
        value: a.id,
        label: `${a.primer_nombre ?? ''} ${a.primer_apellido ?? ''} (${a.identificacion ?? ''})`.trim(),
      })),
    };
  },
});
