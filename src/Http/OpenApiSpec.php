<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Genera la especificacion OpenAPI 3.0 de la FitLife API en PHP puro.
 * Sin dependencias externas: el metodo generate() devuelve un arreglo
 * que index.php serializa como JSON en GET /docs/openapi.json.
 */
final class OpenApiSpec
{
    // -------------------------------------------------------------------------
    // Punto de entrada
    // -------------------------------------------------------------------------

    public static function generate(): array
    {
        return [
            'openapi' => '3.0.3',
            'info'    => [
                'title'       => 'FitLife API',
                'version'     => '1.0.0',
                'description' => 'API REST para la gestión integral del gimnasio FitLife. '
                    . 'Cubre países, estados, ciudades, sedes, afiliados, empleados, '
                    . 'planes, rutinas, ejercicios, horarios, pagos y seguimientos.',
                'contact' => ['name' => 'Equipo FitLife'],
            ],
            'servers' => [
                ['url' => 'http://localhost:8000', 'description' => 'Servidor de desarrollo'],
            ],
            'tags'       => self::buildTags(),
            'paths'      => self::buildPaths(),
            'components' => ['schemas' => self::buildSchemas()],
        ];
    }

    // -------------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------------

    private static function buildTags(): array
    {
        return [
            ['name' => 'Health',               'description' => 'Estado del servicio y base de datos'],
            ['name' => 'Paises',               'description' => 'Gestion de paises'],
            ['name' => 'Estados',              'description' => 'Gestion de estados/departamentos'],
            ['name' => 'Ciudades',             'description' => 'Gestion de ciudades'],
            ['name' => 'Sedes',                'description' => 'Gestion de sedes del gimnasio'],
            ['name' => 'Especialidades',       'description' => 'Especialidades del personal'],
            ['name' => 'Tipos de Documento',   'description' => 'Tipos de documento de identidad'],
            ['name' => 'Empleados',            'description' => 'Gestion de empleados'],
            ['name' => 'Planes Nutricionales', 'description' => 'Planes nutricionales disponibles'],
            ['name' => 'Rutinas',              'description' => 'Rutinas de entrenamiento'],
            ['name' => 'Afiliados',            'description' => 'Gestion de afiliados'],
            ['name' => 'Planes',               'description' => 'Planes de membresia'],
            ['name' => 'Pagos',                'description' => 'Registro de pagos de afiliados'],
            ['name' => 'Ejercicios',           'description' => 'Catalogo de ejercicios'],
            ['name' => 'Clases Grupales',      'description' => 'Clases grupales disponibles'],
            ['name' => 'Horarios',             'description' => 'Horarios de clases grupales'],
            ['name' => 'Seguimientos',         'description' => 'Seguimiento fisico de afiliados'],
            ['name' => 'Ejercicios en Rutina', 'description' => 'Asignacion de ejercicios a rutinas'],
        ];
    }

    // -------------------------------------------------------------------------
    // Paths
    // -------------------------------------------------------------------------

    private static function buildPaths(): array
    {
        return array_merge(
            [
                '/health' => [
                    'get' => [
                        'tags'        => ['Health'],
                        'summary'     => 'Verificar estado del servicio',
                        'operationId' => 'health',
                        'responses'   => [
                            '200' => [
                                'description' => 'Servicio disponible',
                                'content' => ['application/json' => ['schema' => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'service'       => ['type' => 'string', 'example' => 'ok'],
                                        'database'      => ['type' => 'string', 'example' => 'ok'],
                                        'mysql_version' => ['type' => 'string', 'example' => '8.0.33'],
                                    ],
                                ]]],
                            ],
                        ],
                    ],
                ],
            ],
            self::crudPaths('paises',               'Pais',            'Paises',               'pais'),
            self::crudPaths('estados',              'Estado',          'Estados',              'estado'),
            self::crudPaths('ciudades',             'Ciudad',          'Ciudades',             'ciudad'),
            self::crudPaths('sedes',                'Sede',            'Sedes',                'sede'),
            self::crudPaths('especialidades',       'Especialidad',    'Especialidades',       'especialidad'),
            self::crudPaths('tipos-documento',      'TipoDocumento',   'Tipos de Documento',   'tipo de documento'),
            self::crudPaths('empleados',            'Empleado',        'Empleados',            'empleado'),
            self::crudPaths('planes-nutricionales', 'PlanNutricional', 'Planes Nutricionales', 'plan nutricional'),
            self::crudPaths('rutinas',              'Rutina',          'Rutinas',              'rutina'),
            self::crudPaths('afiliados',            'Afiliado',        'Afiliados',            'afiliado'),
            self::crudPaths('planes',               'Plan',            'Planes',               'plan'),
            self::crudPaths('pagos',                'Pago',            'Pagos',                'pago'),
            self::crudPaths('ejercicios',           'Ejercicio',       'Ejercicios',           'ejercicio'),
            self::crudPaths('clases-grupales',      'ClaseGrupal',     'Clases Grupales',      'clase grupal'),
            self::crudPaths('horarios',             'Horario',         'Horarios',             'horario'),
            self::crudPaths('seguimientos',         'Seguimiento',     'Seguimientos',         'seguimiento'),
            self::crudPaths('ejercicios-rutina',    'EjercicioRutina', 'Ejercicios en Rutina', 'ejercicio en rutina'),
        );
    }

    /**
     * Genera las 5 operaciones CRUD estandar para un recurso REST.
     *
     * GET    /{resource}      → index
     * POST   /{resource}      → store
     * GET    /{resource}/{id} → show
     * PUT    /{resource}/{id} → replace
     * DELETE /{resource}/{id} → destroy
     */
    private static function crudPaths(string $resource, string $schema, string $tag, string $singular): array
    {
        $schemaRef = ['$ref' => "#/components/schemas/{$schema}"];
        $inputRef  = ['$ref' => "#/components/schemas/{$schema}Input"];

        // Convierte "ejercicios-rutina" → "EjerciciosRutina" para el operationId.
        $pascal = str_replace(' ', '', ucwords(str_replace('-', ' ', $resource)));

        return [
            "/{$resource}" => [
                'get' => [
                    'tags'        => [$tag],
                    'summary'     => "Listar {$tag}",
                    'operationId' => "list{$pascal}",
                    'responses'   => [
                        '200' => [
                            'description' => 'Listado exitoso',
                            'content'     => ['application/json' => ['schema' => [
                                'type'       => 'object',
                                'properties' => [
                                    'data' => ['type' => 'array', 'items' => $schemaRef],
                                ],
                            ]]],
                        ],
                    ],
                ],
                'post' => [
                    'tags'        => [$tag],
                    'summary'     => "Crear {$singular}",
                    'operationId' => "create{$pascal}",
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => $inputRef]],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => ucfirst($singular) . ' creado exitosamente',
                            'content'     => ['application/json' => ['schema' => [
                                'type'       => 'object',
                                'properties' => [
                                    'message' => ['type' => 'string'],
                                    'data'    => $schemaRef,
                                ],
                            ]]],
                        ],
                        '422' => self::unprocessable(),
                    ],
                ],
            ],
            "/{$resource}/{id}" => [
                'parameters' => [self::idParam($singular)],
                'get' => [
                    'tags'        => [$tag],
                    'summary'     => "Obtener {$singular} por ID",
                    'operationId' => "get{$pascal}ById",
                    'responses'   => [
                        '200' => [
                            'description' => ucfirst($singular) . ' encontrado',
                            'content'     => ['application/json' => ['schema' => [
                                'type'       => 'object',
                                'properties' => ['data' => $schemaRef],
                            ]]],
                        ],
                        '400' => self::badRequest(),
                        '404' => self::notFound(),
                    ],
                ],
                'put' => [
                    'tags'        => [$tag],
                    'summary'     => "Actualizar {$singular}",
                    'operationId' => "update{$pascal}",
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => $inputRef]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => ucfirst($singular) . ' actualizado exitosamente',
                            'content'     => ['application/json' => ['schema' => self::messageSchema()]],
                        ],
                        '400' => self::badRequest(),
                        '404' => self::notFound(),
                        '422' => self::unprocessable(),
                    ],
                ],
                'delete' => [
                    'tags'        => [$tag],
                    'summary'     => "Eliminar {$singular}",
                    'operationId' => "delete{$pascal}",
                    'responses'   => [
                        '200' => [
                            'description' => ucfirst($singular) . ' eliminado exitosamente',
                            'content'     => ['application/json' => ['schema' => self::messageSchema()]],
                        ],
                        '400' => self::badRequest(),
                        '404' => self::notFound(),
                    ],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers de respuestas comunes
    // -------------------------------------------------------------------------

    private static function idParam(string $singular): array
    {
        return [
            'name'        => 'id',
            'in'          => 'path',
            'required'    => true,
            'description' => "ID del {$singular}",
            'schema'      => ['type' => 'integer', 'minimum' => 1],
        ];
    }

    private static function messageSchema(): array
    {
        return ['type' => 'object', 'properties' => ['message' => ['type' => 'string']]];
    }

    private static function notFound(): array
    {
        return ['description' => 'Recurso no encontrado',
            'content' => ['application/json' => ['schema' => self::messageSchema()]]];
    }

    private static function badRequest(): array
    {
        return ['description' => 'ID invalido',
            'content' => ['application/json' => ['schema' => self::messageSchema()]]];
    }

    private static function unprocessable(): array
    {
        return ['description' => 'Error de validacion',
            'content' => ['application/json' => ['schema' => [
                'type'       => 'object',
                'properties' => ['errors' => ['type' => 'array', 'items' => ['type' => 'string']]],
            ]]]];
    }

    // -------------------------------------------------------------------------
    // Schemas de componentes
    // -------------------------------------------------------------------------

    private static function buildSchemas(): array
    {
        return [

            // -----------------------------------------------------------------
            // Pais
            // -----------------------------------------------------------------
            'Pais' => [
                'type'       => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'     => ['type' => 'string',  'example' => 'Colombia'],
                    'cod_postal' => ['type' => 'string',  'example' => 'CO'],
                ],
            ],
            'PaisInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'cod_postal'],
                'properties' => [
                    'nombre'     => ['type' => 'string', 'example' => 'Colombia'],
                    'cod_postal' => ['type' => 'string', 'example' => 'CO'],
                ],
            ],

            // -----------------------------------------------------------------
            // Estado
            // -----------------------------------------------------------------
            'Estado' => [
                'type'       => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'     => ['type' => 'string',  'example' => 'Cundinamarca'],
                    'cod_postal' => ['type' => 'string',  'example' => 'CUN'],
                    'paises_id'  => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'EstadoInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'cod_postal', 'paises_id'],
                'properties' => [
                    'nombre'     => ['type' => 'string',  'example' => 'Cundinamarca'],
                    'cod_postal' => ['type' => 'string',  'example' => 'CUN'],
                    'paises_id'  => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // Ciudad
            // -----------------------------------------------------------------
            'Ciudad' => [
                'type'       => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'     => ['type' => 'string',  'example' => 'Bogota'],
                    'cod_postal' => ['type' => 'string',  'example' => '110111'],
                    'estado_id'  => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'CiudadInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'cod_postal', 'estado_id'],
                'properties' => [
                    'nombre'     => ['type' => 'string',  'example' => 'Bogota'],
                    'cod_postal' => ['type' => 'string',  'example' => '110111'],
                    'estado_id'  => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // Sede
            // -----------------------------------------------------------------
            'Sede' => [
                'type'       => 'object',
                'properties' => [
                    'id'        => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'direccion' => ['type' => 'string',  'example' => 'Cra 7 # 32-16'],
                    'telefono'  => ['type' => 'string',  'example' => '3001234567'],
                    'ciudad_id' => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'SedeInput' => [
                'type'       => 'object',
                'required'   => ['direccion', 'telefono', 'ciudad_id'],
                'properties' => [
                    'direccion' => ['type' => 'string',  'example' => 'Cra 7 # 32-16'],
                    'telefono'  => ['type' => 'string',  'example' => '3001234567'],
                    'ciudad_id' => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // Especialidad
            // -----------------------------------------------------------------
            'Especialidad' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'      => ['type' => 'string',  'example' => 'Nutricion Deportiva'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Asesoria en alimentacion para deportistas'],
                ],
            ],
            'EspecialidadInput' => [
                'type'       => 'object',
                'required'   => ['nombre'],
                'properties' => [
                    'nombre'      => ['type' => 'string', 'example' => 'Nutricion Deportiva'],
                    'descripcion' => ['type' => 'string', 'nullable' => true, 'example' => 'Asesoria en alimentacion para deportistas'],
                ],
            ],

            // -----------------------------------------------------------------
            // TipoDocumento
            // -----------------------------------------------------------------
            'TipoDocumento' => [
                'type'       => 'object',
                'properties' => [
                    'id'             => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'tipo_documento' => ['type' => 'string',  'example' => 'Cedula de Ciudadania'],
                    'sigla'          => ['type' => 'string',  'nullable' => true, 'example' => 'CC'],
                ],
            ],
            'TipoDocumentoInput' => [
                'type'       => 'object',
                'required'   => ['tipo_documento'],
                'properties' => [
                    'tipo_documento' => ['type' => 'string', 'example' => 'Cedula de Ciudadania'],
                    'sigla'          => ['type' => 'string', 'nullable' => true, 'example' => 'CC'],
                ],
            ],

            // -----------------------------------------------------------------
            // Empleado
            // -----------------------------------------------------------------
            'Empleado' => [
                'type'       => 'object',
                'properties' => [
                    'id'                => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'identificacion'    => ['type' => 'string',  'example' => '12345678'],
                    'primer_nombre'     => ['type' => 'string',  'example' => 'Carlos'],
                    'segundo_nombre'    => ['type' => 'string',  'nullable' => true, 'example' => 'Andres'],
                    'primer_apellido'   => ['type' => 'string',  'example' => 'Gomez'],
                    'segundo_apellido'  => ['type' => 'string',  'nullable' => true, 'example' => 'Perez'],
                    'salario'           => ['type' => 'number',  'format' => 'float', 'example' => 2500000.00],
                    'fecha_ingreso'     => ['type' => 'string',  'format' => 'date',  'example' => '2024-01-15'],
                    'sede_id'           => ['type' => 'integer', 'example' => 1],
                    'especialidad_id'   => ['type' => 'integer', 'example' => 1],
                    'tipo_documento_id' => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'EmpleadoInput' => [
                'type'     => 'object',
                'required' => ['identificacion', 'primer_nombre', 'primer_apellido', 'salario', 'fecha_ingreso', 'sede_id', 'especialidad_id', 'tipo_documento_id'],
                'properties' => [
                    'identificacion'    => ['type' => 'string',  'example' => '12345678'],
                    'primer_nombre'     => ['type' => 'string',  'example' => 'Carlos'],
                    'segundo_nombre'    => ['type' => 'string',  'nullable' => true, 'example' => 'Andres'],
                    'primer_apellido'   => ['type' => 'string',  'example' => 'Gomez'],
                    'segundo_apellido'  => ['type' => 'string',  'nullable' => true, 'example' => 'Perez'],
                    'salario'           => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 2500000.00],
                    'fecha_ingreso'     => ['type' => 'string',  'format' => 'date', 'example' => '2024-01-15'],
                    'sede_id'           => ['type' => 'integer', 'example' => 1],
                    'especialidad_id'   => ['type' => 'integer', 'example' => 1],
                    'tipo_documento_id' => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // PlanNutricional
            // -----------------------------------------------------------------
            'PlanNutricional' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'      => ['type' => 'string',  'example' => 'Plan Proteico'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Alto en proteinas para masa muscular'],
                ],
            ],
            'PlanNutricionalInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'descripcion'],
                'properties' => [
                    'nombre'      => ['type' => 'string', 'example' => 'Plan Proteico'],
                    'descripcion' => ['type' => 'string', 'example' => 'Alto en proteinas para masa muscular'],
                ],
            ],

            // -----------------------------------------------------------------
            // Rutina
            // -----------------------------------------------------------------
            'Rutina' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'      => ['type' => 'string',  'example' => 'Rutina de Fuerza'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Entrenamiento de fuerza 5 dias/semana'],
                ],
            ],
            'RutinaInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'descripcion'],
                'properties' => [
                    'nombre'      => ['type' => 'string', 'example' => 'Rutina de Fuerza'],
                    'descripcion' => ['type' => 'string', 'example' => 'Entrenamiento de fuerza 5 dias/semana'],
                ],
            ],

            // -----------------------------------------------------------------
            // Afiliado
            // -----------------------------------------------------------------
            'Afiliado' => [
                'type'       => 'object',
                'properties' => [
                    'id'                  => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'identificacion'      => ['type' => 'string',  'example' => '98765432'],
                    'primer_nombre'       => ['type' => 'string',  'example' => 'Laura'],
                    'segundo_nombre'      => ['type' => 'string',  'nullable' => true, 'example' => 'Sofia'],
                    'primer_apellido'     => ['type' => 'string',  'example' => 'Martinez'],
                    'segundo_apellido'    => ['type' => 'string',  'nullable' => true, 'example' => 'Lopez'],
                    'correo_electronico'  => ['type' => 'string',  'format' => 'email', 'example' => 'laura@example.com'],
                    'fecha_nacimiento'    => ['type' => 'string',  'format' => 'date',  'example' => '1995-06-20'],
                    'id_tipo_documento'   => ['type' => 'integer', 'example' => 1],
                    'id_plan_nutricional' => ['type' => 'integer', 'example' => 1],
                    'rutina_id'           => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'AfiliadoInput' => [
                'type'     => 'object',
                'required' => ['identificacion', 'primer_nombre', 'primer_apellido', 'correo_electronico', 'fecha_nacimiento', 'id_tipo_documento', 'id_plan_nutricional', 'rutina_id'],
                'properties' => [
                    'identificacion'      => ['type' => 'string',  'example' => '98765432'],
                    'primer_nombre'       => ['type' => 'string',  'example' => 'Laura'],
                    'segundo_nombre'      => ['type' => 'string',  'nullable' => true, 'example' => 'Sofia'],
                    'primer_apellido'     => ['type' => 'string',  'example' => 'Martinez'],
                    'segundo_apellido'    => ['type' => 'string',  'nullable' => true, 'example' => 'Lopez'],
                    'correo_electronico'  => ['type' => 'string',  'format' => 'email', 'example' => 'laura@example.com'],
                    'fecha_nacimiento'    => ['type' => 'string',  'format' => 'date',  'example' => '1995-06-20'],
                    'id_tipo_documento'   => ['type' => 'integer', 'example' => 1],
                    'id_plan_nutricional' => ['type' => 'integer', 'example' => 1],
                    'rutina_id'           => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // Plan
            // -----------------------------------------------------------------
            'Plan' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'      => ['type' => 'string',  'example' => 'Plan Basico'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Acceso a equipos basicos'],
                    'valor'       => ['type' => 'number',  'format' => 'float', 'example' => 80000.00],
                ],
            ],
            'PlanInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'valor'],
                'properties' => [
                    'nombre'      => ['type' => 'string',  'example' => 'Plan Basico'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Acceso a equipos basicos'],
                    'valor'       => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 80000.00],
                ],
            ],

            // -----------------------------------------------------------------
            // Pago
            // -----------------------------------------------------------------
            'Pago' => [
                'type'       => 'object',
                'properties' => [
                    'id'           => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'plan_id'      => ['type' => 'integer', 'example' => 1],
                    'afiliado_id'  => ['type' => 'integer', 'example' => 1],
                    'nro_recibo'   => ['type' => 'string',  'example' => 'REC-2024-001'],
                    'fecha_pago'   => ['type' => 'string',  'format' => 'date',  'example' => '2024-05-01'],
                    'valor_pagado' => ['type' => 'number',  'format' => 'float', 'example' => 80000.00],
                    'metodo_pago'  => ['type' => 'string',  'example' => 'Transferencia'],
                ],
            ],
            'PagoInput' => [
                'type'     => 'object',
                'required' => ['plan_id', 'afiliado_id', 'nro_recibo', 'fecha_pago', 'valor_pagado', 'metodo_pago'],
                'properties' => [
                    'plan_id'      => ['type' => 'integer', 'example' => 1],
                    'afiliado_id'  => ['type' => 'integer', 'example' => 1],
                    'nro_recibo'   => ['type' => 'string',  'example' => 'REC-2024-001'],
                    'fecha_pago'   => ['type' => 'string',  'format' => 'date',  'example' => '2024-05-01'],
                    'valor_pagado' => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 80000.00],
                    'metodo_pago'  => ['type' => 'string',  'example' => 'Transferencia'],
                ],
            ],

            // -----------------------------------------------------------------
            // Ejercicio
            // -----------------------------------------------------------------
            'Ejercicio' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'      => ['type' => 'string',  'example' => 'Press de Banca'],
                    'descripcion' => ['type' => 'string',  'nullable' => true, 'example' => 'Ejercicio para pecho con barra'],
                    'imagen'      => ['type' => 'string',  'nullable' => true, 'example' => 'press_banca.jpg'],
                    'maquina'     => ['type' => 'string',  'nullable' => true, 'example' => 'Banca plana'],
                ],
            ],
            'EjercicioInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'descripcion'],
                'properties' => [
                    'nombre'      => ['type' => 'string', 'example' => 'Press de Banca'],
                    'descripcion' => ['type' => 'string', 'example' => 'Ejercicio para pecho con barra'],
                    'imagen'      => ['type' => 'string', 'nullable' => true, 'example' => 'press_banca.jpg'],
                    'maquina'     => ['type' => 'string', 'nullable' => true, 'example' => 'Banca plana'],
                ],
            ],

            // -----------------------------------------------------------------
            // ClaseGrupal
            // -----------------------------------------------------------------
            'ClaseGrupal' => [
                'type'       => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'nombre'     => ['type' => 'string',  'example' => 'Spinning'],
                    'intensidad' => ['type' => 'string',  'enum' => ['BAJA', 'MEDIA', 'ALTA'], 'example' => 'ALTA'],
                ],
            ],
            'ClaseGrupalInput' => [
                'type'       => 'object',
                'required'   => ['nombre', 'intensidad'],
                'properties' => [
                    'nombre'     => ['type' => 'string', 'example' => 'Spinning'],
                    'intensidad' => ['type' => 'string', 'enum' => ['BAJA', 'MEDIA', 'ALTA'], 'example' => 'ALTA'],
                ],
            ],

            // -----------------------------------------------------------------
            // Horario
            // -----------------------------------------------------------------
            'Horario' => [
                'type'       => 'object',
                'properties' => [
                    'id'              => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'id_clase_grupal' => ['type' => 'integer', 'example' => 1],
                    'id_empleado'     => ['type' => 'integer', 'example' => 1],
                    'fecha_inicio'    => ['type' => 'string',  'format' => 'date', 'example' => '2024-06-01'],
                    'fecha_fin'       => ['type' => 'string',  'format' => 'date', 'example' => '2024-08-31'],
                    'hora_inicio'     => ['type' => 'string',  'example' => '07:00:00'],
                    'hora_fin'        => ['type' => 'string',  'example' => '08:00:00'],
                ],
            ],
            'HorarioInput' => [
                'type'     => 'object',
                'required' => ['id_clase_grupal', 'id_empleado', 'fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin'],
                'properties' => [
                    'id_clase_grupal' => ['type' => 'integer', 'example' => 1],
                    'id_empleado'     => ['type' => 'integer', 'example' => 1],
                    'fecha_inicio'    => ['type' => 'string',  'format' => 'date', 'example' => '2024-06-01'],
                    'fecha_fin'       => ['type' => 'string',  'format' => 'date', 'example' => '2024-08-31'],
                    'hora_inicio'     => ['type' => 'string',  'example' => '07:00:00'],
                    'hora_fin'        => ['type' => 'string',  'example' => '08:00:00'],
                ],
            ],

            // -----------------------------------------------------------------
            // Seguimiento
            // -----------------------------------------------------------------
            'Seguimiento' => [
                'type'       => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'fecha'       => ['type' => 'string',  'format' => 'date',  'example' => '2024-05-15'],
                    'peso'        => ['type' => 'number',  'format' => 'float', 'example' => 72.5],
                    'altura'      => ['type' => 'number',  'format' => 'float', 'example' => 1.75],
                    'imc'         => ['type' => 'number',  'format' => 'float', 'example' => 23.67],
                    'id_afiliado' => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'SeguimientoInput' => [
                'type'     => 'object',
                'required' => ['fecha', 'peso', 'altura', 'imc', 'id_afiliado'],
                'properties' => [
                    'fecha'       => ['type' => 'string',  'format' => 'date',  'example' => '2024-05-15'],
                    'peso'        => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 72.5],
                    'altura'      => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 1.75],
                    'imc'         => ['type' => 'number',  'format' => 'float', 'minimum' => 0, 'example' => 23.67],
                    'id_afiliado' => ['type' => 'integer', 'example' => 1],
                ],
            ],

            // -----------------------------------------------------------------
            // EjercicioRutina
            // -----------------------------------------------------------------
            'EjercicioRutina' => [
                'type'       => 'object',
                'properties' => [
                    'id'           => ['type' => 'integer', 'readOnly' => true, 'example' => 1],
                    'ciclos'       => ['type' => 'integer', 'example' => 4],
                    'repeticiones' => ['type' => 'integer', 'example' => 12],
                    'id_ejercicio' => ['type' => 'integer', 'example' => 1],
                    'id_rutina'    => ['type' => 'integer', 'example' => 1],
                ],
            ],
            'EjercicioRutinaInput' => [
                'type'     => 'object',
                'required' => ['ciclos', 'repeticiones', 'id_ejercicio', 'id_rutina'],
                'properties' => [
                    'ciclos'       => ['type' => 'integer', 'minimum' => 1, 'example' => 4],
                    'repeticiones' => ['type' => 'integer', 'minimum' => 1, 'example' => 12],
                    'id_ejercicio' => ['type' => 'integer', 'example' => 1],
                    'id_rutina'    => ['type' => 'integer', 'example' => 1],
                ],
            ],
        ];
    }
}
