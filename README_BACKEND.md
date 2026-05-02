# FitLife API (Base PHP + MySQL)

Estructura inicial con buenas practicas para backend en PHP:

- Configuracion por entorno con `.env`
- Conexion a MySQL usando `PDO` con opciones seguras
- Carga por `Composer` y autoload `PSR-4`
- Punto de entrada unico en `public/index.php`
- Ruta de verificacion: `GET /health`

## 1. Instalacion

```bash
composer install
```

## 2. Configurar entorno

1. Copia `.env.example` como `.env`
2. Ajusta tus credenciales de MySQL

Variables clave:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET`

## 3. Levantar servidor local

```bash
composer serve
```

## 4. Probar conexion

Abre:

```text
http://localhost:8000/health
```

Si la conexion funciona, devuelve estado de servicio y version de MySQL.

## Estructura

```text
public/
  index.php
src/
  bootstrap.php
  Config/
    Config.php
    DatabaseConfig.php
  Core/
    App.php
    ErrorHandler.php
  Database/
    Connection.php
  Domain/
    Entity/
      AbstractEntity.php
      EntityInterface.php
      Afiliado.php
      Ciudad.php
      ClaseGrupal.php
      Ejercicio.php
      EjercicioRutina.php
      Empleado.php
      Especialidad.php
      Estado.php
      Horario.php
      Pago.php
      Pais.php
      Plan.php
      PlanNutricional.php
      Rutina.php
      Sede.php
      Seguimiento.php
      TipoDocumento.php
storage/
  logs/
tests/
```

## Dominio inicial (alineado a BD)

Se agrego una entidad por tabla del esquema para facilitar el crecimiento por capas
(repositorios, servicios y controladores) sin mezclar SQL con logica de negocio.

Cada entidad incluye:

- `TABLE`: nombre de tabla real en MySQL
- `columns()`: columnas persistentes
- `fromArray()` y `toArray()`: mapeo simple de entrada/salida

Tablas cubiertas: `paises`, `estados`, `ciudades`, `sedes`, `especialidades`,
`tipos_documento`, `empleados`, `planes_nutricionales`, `rutinas`, `afiliados`,
`planes`, `pagos`, `ejercicios`, `clases_grupales`, `horarios`, `seguimientos`,
`ejercicios_rutina`.
