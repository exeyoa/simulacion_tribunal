-- Base de datos del Tribunal Electoral (simulación académica)
-- Para importar en un hosting: entrar a phpMyAdmin, seleccionar la base ya
-- creada y pegar el contenido; estas líneas se dejan comentadas:
-- CREATE DATABASE IF NOT EXISTS tribunal_electoral_db
--     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE tribunal_electoral_db;

CREATE TABLE IF NOT EXISTS ciudadanos (
    cedula VARCHAR(20) NOT NULL,
    primer_nombre VARCHAR(100) NOT NULL,
    segundo_nombre VARCHAR(100) NULL,
    primer_apellido VARCHAR(100) NOT NULL,
    segundo_apellido VARCHAR(100) NULL,
    fecha_nacimiento DATE NOT NULL,
    sexo ENUM('M','F') NOT NULL,
    lugar_nacimiento VARCHAR(100) NOT NULL,
    donacion_organos BOOLEAN NOT NULL DEFAULT FALSE,
    nombre_padre VARCHAR(100) NULL,
    nombre_madre VARCHAR(100) NULL,
    fecha_expedicion DATE NOT NULL,
    fecha_expiracion DATE NOT NULL,
    foto VARCHAR(255) NOT NULL,
    PRIMARY KEY (cedula)
);

INSERT INTO ciudadanos
    (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
     fecha_nacimiento, sexo, lugar_nacimiento, donacion_organos, nombre_padre,
     nombre_madre, fecha_expedicion, fecha_expiracion, foto)
VALUES
    ('8-123-456', 'María', 'Fernanda', 'Castillo', 'Ríos', '1990-05-14', 'F', 'Panamá', 1,
     'Jorge Castillo', 'Lucía Ríos', '2015-03-10', '2030-03-10', 'fotos_cedulas/placeholder.jpg'),
    ('8-987-321', 'Luis', 'Alberto', 'Mendoza', 'Vega', '1985-11-02', 'M', 'Panamá', 0,
     'Raúl Mendoza', 'Carmen Vega', '2012-07-15', '2027-07-15', 'fotos_cedulas/placeholder.jpg'),
    ('4-555-789', 'Ana', 'Lucía', 'Herrera', 'Solís', '1998-02-27', 'F', 'Colón', 1,
     'Pedro Herrera', 'Marta Solís', '2018-05-22', '2033-05-22', 'fotos_cedulas/placeholder.jpg'),
    ('6-210-345', 'Carlos', 'Enrique', 'Díaz', 'López', '1972-09-08', 'M', 'Veraguas', 0,
     'Hugo Díaz', 'Rosa López', '2010-01-30', '2025-01-30', 'fotos_cedulas/placeholder.jpg'),
    ('7-654-210', 'Sofía', 'Beatriz', 'Toro', 'Navarro', '2001-12-19', 'F', 'Los Santos', 1,
     'Rubén Toro', 'Inés Navarro', '2019-08-11', '2034-08-11', 'fotos_cedulas/placeholder.jpg'),
    ('10-432-876', 'Jorge', 'Luis', 'Núñez', 'Quintana', '1988-04-03', 'M', 'Chiriquí', 1,
     'Ramón Núñez', 'Elsa Quintana', '2013-09-17', '2028-09-17', 'fotos_cedulas/placeholder.jpg'),
    ('13-111-258', 'Patricia', 'del Carmen', 'Franco', 'Alemán', '1995-07-25', 'F', 'Bocas del Toro', 0,
     'Simón Franco', 'Gladys Alemán', '2016-11-05', '2031-11-05', 'fotos_cedulas/placeholder.jpg'),
    ('2-778-963', 'Ricardo', 'Antonio', 'Peña', 'Vargas', '1968-06-30', 'M', 'Coclé', 1,
     'Félix Peña', 'Nora Vargas', '2009-12-01', '2024-12-01', 'fotos_cedulas/placeholder.jpg'),
    ('9-333-147', 'Daniela', 'María', 'Castro', 'Beltrán', '2003-08-16', 'F', 'Darién', 0,
     'Iván Castro', 'Sheila Beltrán', '2021-04-19', '2036-04-19', 'fotos_cedulas/placeholder.jpg'),
    ('12-999-654', 'Óscar', 'Manuel', 'Cedeño', 'Jaén', '1980-01-21', 'M', 'Panamá Oeste', 1,
     'Abel Cedeño', 'Doris Jaén', '2011-06-14', '2026-06-14', 'fotos_cedulas/placeholder.jpg');