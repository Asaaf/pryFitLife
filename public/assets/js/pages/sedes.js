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
  endpoint:    '/sedes',
  title:       'Sedes',
  singular:    'sede',
  formColumns: 2,
  columns: [
    { key: 'id',        label: 'ID'        },
    { key: 'direccion', label: 'Dirección' },
    { key: 'telefono',  label: 'Teléfono'  },
    { key: 'ciudad_id', label: 'ID Ciudad' },
  ],
  fields: [
    { key: 'direccion', label: 'Dirección', type: 'text',   required: true  },
    { key: 'telefono',  label: 'Teléfono',  type: 'text',   required: true  },
    { key: 'pais_id',   label: 'País',      type: 'select', required: true  },
    { key: 'estado_id', label: 'Departamento', type: 'select', required: true  },
    { key: 'ciudad_id', label: 'Ciudad',    type: 'select', required: true  },
  ],
  loadRelated: async () => {
    const res = await api.get('/paises');
    return {
      pais_id: toOptions(res.data),
      estado_id: [],
      ciudad_id: [],
    };
  },
  onModalReady: async ({ item, related, setOptions, setValue }) => {
    const paisSelect = document.getElementById('field_pais_id');
    const estadoSelect = document.getElementById('field_estado_id');

    setOptions('pais_id', related.pais_id ?? []);
    setOptions('estado_id', []);
    setOptions('ciudad_id', []);

    const loadEstados = async (paisId, selectedEstadoId = '') => {
      if (!paisId) {
        setOptions('estado_id', []);
        setOptions('ciudad_id', []);
        setValue('estado_id', '');
        setValue('ciudad_id', '');
        return;
      }

      const estados = await fetchOptions(`/estados?paises_id=${encodeURIComponent(paisId)}`);
      setOptions('estado_id', estados, selectedEstadoId);
      if (!selectedEstadoId) {
        setOptions('ciudad_id', []);
        setValue('ciudad_id', '');
      }
    };

    const loadCiudades = async (estadoId, selectedCiudadId = '') => {
      if (!estadoId) {
        setOptions('ciudad_id', []);
        setValue('ciudad_id', '');
        return;
      }

      const ciudades = await fetchOptions(`/ciudades?estado_id=${encodeURIComponent(estadoId)}`);
      setOptions('ciudad_id', ciudades, selectedCiudadId);
    };

    paisSelect?.addEventListener('change', async e => {
      const paisId = e.target.value;
      await loadEstados(paisId);
    });

    estadoSelect?.addEventListener('change', async e => {
      const estadoId = e.target.value;
      await loadCiudades(estadoId);
    });

    if (!item?.ciudad_id) {
      return;
    }

    const ciudadRes = await api.get(`/ciudades/${item.ciudad_id}`);
    if (!ciudadRes.ok || !ciudadRes.data?.estado_id) {
      return;
    }

    const estadoRes = await api.get(`/estados/${ciudadRes.data.estado_id}`);
    if (!estadoRes.ok || !estadoRes.data?.paises_id) {
      return;
    }

    setValue('pais_id', String(estadoRes.data.paises_id));
    await loadEstados(String(estadoRes.data.paises_id), String(ciudadRes.data.estado_id));
    setValue('estado_id', String(ciudadRes.data.estado_id));
    await loadCiudades(String(ciudadRes.data.estado_id), String(item.ciudad_id));
    setValue('ciudad_id', String(item.ciudad_id));
  },
});
