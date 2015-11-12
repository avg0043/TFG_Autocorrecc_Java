DROP TABLE IF EXISTS intentos;
DROP TABLE IF EXISTS tests;
DROP TABLE IF EXISTS tareas;
DROP TABLE IF EXISTS alumnos;
DROP TABLE IF EXISTS profesores;


CREATE TABLE profesores
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	nombre VARCHAR(45) NOT NULL,
	apellidos VARCHAR(45) NOT NULL,
	consumer_key VARCHAR(45) NOT NULL,
	secret VARCHAR(45) NOT NULL,
	correo VARCHAR(45) NOT NULL,
	CONSTRAINT pk01_profesores PRIMARY KEY(id)
);

CREATE TABLE alumnos
(
	id INTEGER NOT NULL,
	nombre VARCHAR(45) NOT NULL,
	apellidos VARCHAR(45) NOT NULL,
	correo VARCHAR(45) NOT NULL,
	CONSTRAINT pk01_alumnos PRIMARY KEY(id)
);

CREATE TABLE tareas
(
	id INTEGER NOT NULL,
	profesor_id INTEGER NOT NULL,
	nombre VARCHAR(45) NOT NULL,
	num_max_intentos INTEGER NOT NULL,
	fecha_limite DATETIME NOT NULL,
	fecha_modificacion DATETIME NOT NULL,
	CONSTRAINT pk01_tareas PRIMARY KEY(id),
	CONSTRAINT fk01_tareas FOREIGN KEY(profesor_id)
		REFERENCES profesores (id)
);

CREATE TABLE tests
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	tarea_id INTEGER NOT NULL,
	nombre VARCHAR(45) NOT NULL,
	fecha_subida DATETIME NOT NULL,
	CONSTRAINT pk01_tests PRIMARY KEY(id),
	CONSTRAINT fk01_tests FOREIGN KEY(tarea_id)
		REFERENCES tareas (id),
	CONSTRAINT uk01_tests UNIQUE KEY(tarea_id, nombre)
);

CREATE TABLE intentos
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	tarea_id INTEGER NOT NULL,
	alumno_id INTEGER NOT NULL,
	resultado TINYINT(1) NOT NULL,
	fecha_intento DATETIME NOT NULL,
	CONSTRAINT pk01_intentos PRIMARY KEY(id),
	CONSTRAINT fk01_intentos FOREIGN KEY
		(tarea_id) REFERENCES tareas (id),
	CONSTRAINT fk02_intentos FOREIGN KEY
		(alumno_id) REFERENCES alumnos (id)	
);