import { createCrudPage } from './crud.js';
import { api }            from '../api.js';

function toOptions(items) {
  return (items ?? []).map(item => ({ value: item.id, label: item.nombre }));
}

async function fetchOptions(path) {
  const res = await api.get(path);
  return res.ok ? toOptions(res.data) : [];
}

export default createCrudPage({
  endpoint:    '/ciudades',
  title:       'Ciudades',
  singular:    'ciudad',
  formColumns: 2,
  columns: [
    { key: 'id',         label: 'ID'          },
    { key: 'nombre',     label: 'Nombre'      },
    { key: 'cod_postal', label: 'Cód. Postal' },
    { key: 'estado_id',  label: 'ID Estado'   },
  ],
  fields: [
    { key: 'nombre',     label: 'Nombre',       type: 'text',   required: true },
    { key: 'cod_postal', label: 'Código Postal', type: 'text',   required: true },
    { key: 'pais_id',    label: 'País',          type: 'select', required: true },
    { key: 'estado_id',  label: 'Estado',        type: 'select', required: true },
  ],
  loadRelated: async () => {
    const res = await api.get('/paises');
    return {
      pais_id: toOptions(res.data),
      estado_id: [],
    };
  },
  onModalReady: async ({ item, related, setOptions, setValue }) => {
    const paisSelect = document.getElementById('field_pais_id');

    setOptions('pais_id', related.pais_id ?? []);
    setOptions('estado_id', []);

    const loadEstados = async (paisId, selectedEstadoId = '') => {
      if (!paisId) {
        setOptions('estado_id', []);
        setValue('estado_id', '');
        return;
      }

      const estados = await fetchOptions(`/estados?paises_id=${encodeURIComponent(paisId)}`);
      setOptions('estado_id', estados, selectedEstadoId);
    };

    paisSelect?.addEventListener('change', async e => {
      const paisId = e.target.value;
      await loadEstados(paisId);
    });

    if (!item?.estado_id) {
      return;
    }

    const estadoRes = await api.get(`/estados/${item.estado_id}`);
    if (!estadoRes.ok || !estadoRes.data?.paises_id) {
      return;
    }

    setValue('pais_id', String(estadoRes.data.paises_id));
    await loadEstados(String(estadoRes.data.paises_id), String(item.estado_id));
    setValue('estado_id', String(item.estado_id));
  },
});
