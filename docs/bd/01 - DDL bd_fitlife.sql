-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema bd_fitlife
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema bd_fitlife
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `bd_fitlife` DEFAULT CHARACTER SET utf8 ;
USE `bd_fitlife` ;

-- -----------------------------------------------------
-- Table `bd_fitlife`.`paises`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`paises` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del país',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre del país',
  `cod_postal` VARCHAR(45) NOT NULL COMMENT 'Código postal del país',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que almacena todos los países';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`estados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`estados` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del estado',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre del estado/provincia/departamento',
  `cod_postal` VARCHAR(45) NOT NULL COMMENT 'Código postal del estado/provincia/departamento',
  `paises_id` INT NOT NULL COMMENT 'Id del país al que pertenece el estado/provincia/departamento',
  PRIMARY KEY (`id`),
  INDEX `fk_estados_paises_idx` (`paises_id` ASC) VISIBLE,
  CONSTRAINT `fk_estados_paises`
    FOREIGN KEY (`paises_id`)
    REFERENCES `bd_fitlife`.`paises` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que almacena los estados/provincias/departamentos asociados a un país';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`ciudades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`ciudades` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id de la ciudad',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre de la ciudad',
  `cod_postal` VARCHAR(45) NOT NULL COMMENT 'Código postal de la ciudad',
  `estado_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ciudad_estado1_idx` (`estado_id` ASC) VISIBLE,
  CONSTRAINT `fk_ciudad_estado1`
    FOREIGN KEY (`estado_id`)
    REFERENCES `bd_fitlife`.`estados` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que almacena las ciudades asociadas a un estado';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`sedes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`sedes` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id de la sede',
  `direccion` VARCHAR(250) NOT NULL COMMENT 'Dirección de la sede',
  `telefono` VARCHAR(45) NOT NULL COMMENT 'Teléfono de la sede',
  `ciudad_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sede_ciudad1_idx` (`ciudad_id` ASC) VISIBLE,
  CONSTRAINT `fk_sede_ciudad1`
    FOREIGN KEY (`ciudad_id`)
    REFERENCES `bd_fitlife`.`ciudades` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que almacena las sedes de la empresa';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`especialidades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`especialidades` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id de la especialidad',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre de la especialidad',
  `descripcion` VARCHAR(250) NULL COMMENT 'Descripción de la especialidad',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Especialidad del empleado';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`tipos_documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`tipos_documento` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del tipo de documento',
  `tipo_documento` VARCHAR(60) NOT NULL,
  `sigla` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que almacena los diferentes tipos de documentos';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`empleados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`empleados` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del empleado',
  `identificacion` VARCHAR(45) NOT NULL COMMENT 'Número de documento',
  `primer_nombre` VARCHAR(45) NOT NULL COMMENT 'Primer nombre del empleado',
  `segundo_nombre` VARCHAR(45) NULL COMMENT 'Segundo nombre del empleado',
  `primer_apellido` VARCHAR(45) NOT NULL COMMENT 'Primer apellido del empleado',
  `segundo_apellido` VARCHAR(45) NULL COMMENT 'Segundo apellido del empleado',
  `salario` FLOAT NOT NULL COMMENT 'Salario del empleado',
  `fecha_ingreso` DATE NOT NULL COMMENT 'Fecha en que ingreso a la empresa',
  `sede_id` INT NOT NULL COMMENT 'Id de la sede a la que pertenece el empleado',
  `especialidad_id` INT NOT NULL COMMENT 'Id de la especialidad del empleado',
  `tipo_documento_id` INT NOT NULL COMMENT 'Id del tipo de documento del empleado',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `cedula_UNIQUE` (`identificacion` ASC) VISIBLE,
  INDEX `fk_empleado_sede1_idx` (`sede_id` ASC) VISIBLE,
  INDEX `fk_empleado_especialidad1_idx` (`especialidad_id` ASC) VISIBLE,
  INDEX `fk_empleado_tipo_documento1_idx` (`tipo_documento_id` ASC) VISIBLE,
  CONSTRAINT `fk_empleado_sede1`
    FOREIGN KEY (`sede_id`)
    REFERENCES `bd_fitlife`.`sedes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_empleado_especialidad1`
    FOREIGN KEY (`especialidad_id`)
    REFERENCES `bd_fitlife`.`especialidades` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_empleado_tipo_documento1`
    FOREIGN KEY (`tipo_documento_id`)
    REFERENCES `bd_fitlife`.`tipos_documento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que almacena los empleados vinculados a una sede';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`planes_nutricionales`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`planes_nutricionales` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del plan nutricional',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre del plan nutricional',
  `descripcion` VARCHAR(2000) CHARACTER SET 'armscii8' NOT NULL COMMENT 'Descripción del plan donde se incluye la dieta',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
COMMENT = 'Tabla que contiene todos los planes nutricionales que podría tener un afiliado';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`rutinas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`rutinas` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id de la rutina',
  `nombre` VARCHAR(60) NOT NULL COMMENT 'Nombre de la rutina',
  `descripcion` VARCHAR(1500) NOT NULL COMMENT 'Descripción de la rutina',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que contiene todas las rutinas de los afiliados';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`afiliados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`afiliados` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del afiliado',
  `identificacion` VARCHAR(45) NOT NULL COMMENT 'Identificación del empleado',
  `primer_nombre` VARCHAR(45) NOT NULL COMMENT 'Primer nombre del afiliado',
  `segundo_nombre` VARCHAR(45) NULL COMMENT 'Segundo nombre del afiliado',
  `primer_apellido` VARCHAR(45) NOT NULL COMMENT 'Primer apellido del afiliado',
  `segundo_apellido` VARCHAR(45) NULL COMMENT 'Segundo apellido del afiliado',
  `correo_electronico` VARCHAR(250) NOT NULL COMMENT 'Correo electrónico del afiliado',
  `fecha_nacimiento` DATE NOT NULL COMMENT 'Fecha de nacimiento del afiliado',
  `id_tipo_documento` INT NOT NULL COMMENT 'Id del tipo de documento del afiliado',
  `id_plan_nutricional` INT NOT NULL COMMENT 'Plan nutricional asignado por el entrenador',
  `rutina_id` INT NOT NULL COMMENT 'Id de la rutina',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `correo_electronico_UNIQUE` (`correo_electronico` ASC) VISIBLE,
  INDEX `fk_afiliado_tipo_documento1_idx` (`id_tipo_documento` ASC) VISIBLE,
  INDEX `fk_afiliados_planes_nutricionales1_idx` (`id_plan_nutricional` ASC) VISIBLE,
  INDEX `fk_afiliados_rutinas1_idx` (`rutina_id` ASC) VISIBLE,
  CONSTRAINT `fk_afiliado_tipo_documento1`
    FOREIGN KEY (`id_tipo_documento`)
    REFERENCES `bd_fitlife`.`tipos_documento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_afiliados_planes_nutricionales1`
    FOREIGN KEY (`id_plan_nutricional`)
    REFERENCES `bd_fitlife`.`planes_nutricionales` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_afiliados_rutinas1`
    FOREIGN KEY (`rutina_id`)
    REFERENCES `bd_fitlife`.`rutinas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que almacena los afiliados del gimnasio';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`planes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`planes` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del plan de afiliado',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre del plan de afiliado',
  `descripcion` VARCHAR(250) NULL COMMENT 'Descripción del plan de afiliado',
  `valor` FLOAT NOT NULL COMMENT 'Valor del plan de afiliado',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que contiene los planes de afiliación';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`pagos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`pagos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del pago',
  `plan_id` INT NOT NULL COMMENT 'Id del plan',
  `afiliado_id` INT NOT NULL COMMENT 'Id del afiliado',
  `nro_recibo` INT NOT NULL COMMENT 'Número de recibo',
  `fecha_pago` DATE NOT NULL,
  `valor_pagado` FLOAT NOT NULL COMMENT 'Valor pagado',
  `metodo_pago` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_plan_has_afiliado_afiliado1_idx` (`afiliado_id` ASC) VISIBLE,
  INDEX `fk_plan_has_afiliado_plan1_idx` (`plan_id` ASC) VISIBLE,
  CONSTRAINT `fk_plan_has_afiliado_plan1`
    FOREIGN KEY (`plan_id`)
    REFERENCES `bd_fitlife`.`planes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plan_has_afiliado_afiliado1`
    FOREIGN KEY (`afiliado_id`)
    REFERENCES `bd_fitlife`.`afiliados` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla para almacenar los pagos';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`ejercicios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`ejercicios` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del ejercicio',
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` VARCHAR(1500) NOT NULL COMMENT 'Descripción del ejercicio',
  `imagen` VARCHAR(45) NULL COMMENT 'Imagen del ejercicio',
  `maquina` VARCHAR(45) NULL COMMENT 'Máquina para hacer el ejercicio',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que contiene los ejercicios que puede tener una rutina';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`clases_grupales`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`clases_grupales` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id de la clase grupal',
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre de la clase',
  `intensidad` VARCHAR(45) NOT NULL COMMENT 'Intensidad de la clase (BAJA, MEDIA o ALTA)',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'Tabla que contiene las clases grupales como yoga, crossfit o zumba';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`horarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`horarios` (
  `id` INT NOT NULL COMMENT 'Id del horario',
  `id_clase_grupal` INT NOT NULL COMMENT 'Id de la clase grupal',
  `id_empleado` INT NOT NULL COMMENT 'Id del empleado',
  `fecha_inicio` DATE NOT NULL COMMENT 'Fecha en que inicia la clase grupal con el entrenador asignado',
  `fecha_fin` DATE NOT NULL COMMENT 'Fecha en que finaliza la clase grupal',
  `hora_inicio` TIME NOT NULL COMMENT 'Hora de inicio',
  `hora_fin` TIME NOT NULL COMMENT 'Hora en que finaliza la clase grupal',
  PRIMARY KEY (`id`),
  INDEX `fk_clases_grupales_has_empleados_empleados1_idx` (`id_empleado` ASC) VISIBLE,
  INDEX `fk_clases_grupales_has_empleados_clases_grupales1_idx` (`id_clase_grupal` ASC) VISIBLE,
  CONSTRAINT `fk_clases_grupales_has_empleados_clases_grupales1`
    FOREIGN KEY (`id_clase_grupal`)
    REFERENCES `bd_fitlife`.`clases_grupales` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_clases_grupales_has_empleados_empleados1`
    FOREIGN KEY (`id_empleado`)
    REFERENCES `bd_fitlife`.`empleados` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que contiene los horarios de las clases grupales';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`seguimientos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`seguimientos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Id del seguimiento',
  `fecha` DATE NOT NULL COMMENT 'Fecha en la que se toma el seguimiento biométrico',
  `peso` FLOAT NOT NULL COMMENT 'Peso del afiliado',
  `altura` FLOAT NOT NULL COMMENT 'Altura del afiliado',
  `imc` FLOAT NOT NULL COMMENT 'Indice de masa corporal del afiliado',
  `id_afiliado` INT NOT NULL COMMENT 'Llave foránea del afiliado',
  PRIMARY KEY (`id`),
  INDEX `fk_seguimiento_afiliados1_idx` (`id_afiliado` ASC) VISIBLE,
  CONSTRAINT `fk_seguimiento_afiliados1`
    FOREIGN KEY (`id_afiliado`)
    REFERENCES `bd_fitlife`.`afiliados` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Tabla que contiene el seguimiento biométrico de los afiliados';


-- -----------------------------------------------------
-- Table `bd_fitlife`.`ejercicios_rutina`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bd_fitlife`.`ejercicios_rutina` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Llave primaria de la tabla ejercicios_rutina',
  `ciclos` INT NOT NULL,
  `repeticiones` INT NOT NULL,
  `id_ejercicio` INT NOT NULL COMMENT 'Llave foránea hacia la tabla ejercicios',
  `id_rutina` INT NOT NULL COMMENT 'Llave foránea a la tabla rutinas',
  INDEX `fk_ejercicios_has_rutinas_rutinas1_idx` (`id_rutina` ASC) VISIBLE,
  INDEX `fk_ejercicios_has_rutinas_ejercicios1_idx` (`id_ejercicio` ASC) VISIBLE,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ejercicios_has_rutinas_ejercicios1`
    FOREIGN KEY (`id_ejercicio`)
    REFERENCES `bd_fitlife`.`ejercicios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ejercicios_has_rutinas_rutinas1`
    FOREIGN KEY (`id_rutina`)
    REFERENCES `bd_fitlife`.`rutinas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
