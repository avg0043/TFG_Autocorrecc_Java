DROP TABLE IF EXISTS intentos;
DROP TABLE IF EXISTS tareas;
DROP TABLE IF EXISTS alumnos;
DROP TABLE IF EXISTS profesores;


CREATE TABLE profesores
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	nombre_completo VARCHAR(45) NULL,
	consumer_key VARCHAR(45) NOT NULL,
	secret VARCHAR(45) NOT NULL,
	correo VARCHAR(45) NOT NULL,
	CONSTRAINT pk01_profesores PRIMARY KEY(id)
);

CREATE TABLE alumnos
(
	id INTEGER NOT NULL,
	nombre_completo VARCHAR(45) NOT NULL,
	correo VARCHAR(45) NOT NULL,
	CONSTRAINT pk01_alumnos PRIMARY KEY(id)
);

CREATE TABLE tareas
(
	id INTEGER NOT NULL,
	nombre VARCHAR(45) NOT NULL,
	num_max_intentos INTEGER NOT NULL,
	test TINYINT(1) NULL,
	fecha_limite DATE NOT NULL,
	CONSTRAINT pk01_tareas PRIMARY KEY(id)
);

CREATE TABLE intentos
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	tarea_id INTEGER NOT NULL,
	alumno_id INTEGER NOT NULL,
	CONSTRAINT pk01_intentos PRIMARY KEY(id),
	CONSTRAINT fk01_intentos FOREIGN KEY
		(tarea_id) REFERENCES tareas (id),
	CONSTRAINT fk02_intentos FOREIGN KEY
		(alumno_id) REFERENCES alumnos (id)	
);