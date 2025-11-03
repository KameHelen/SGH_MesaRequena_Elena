DROP DATABASE IF EXISTS sgh;
CREATE DATABASE sgh;
USE sgh;





-- Tabla: usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario'
);

-- Tabla: habitaciones
CREATE TABLE habitaciones (
id INT AUTO_INCREMENT PRIMARY KEY,
numero VARCHAR(20) NOT NULL UNIQUE,
tipo ENUM('Sencilla','Doble','Suite') NOT NULL,
precio_base DECIMAL(10,2) NOT NULL,
estado_limpieza ENUM('Limpia','Sucia','En Limpieza') NOT NULL DEFAULT 'Limpia'
);


-- Tabla: huespedes
CREATE TABLE huespedes (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(150) NOT NULL,
email VARCHAR(150) NOT NULL UNIQUE,
documento_identidad VARCHAR(100) NOT NULL
);


-- Tabla: tareas_mantenimiento
CREATE TABLE tareas_mantenimiento (
id INT AUTO_INCREMENT PRIMARY KEY,
habitacion_id INT NOT NULL,
descripcion TEXT NOT NULL,
fecha_inicio DATE NOT NULL,
fecha_fin DATE NOT NULL,
estado ENUM('Activa','Completada','Cancelada') NOT NULL DEFAULT 'Activa',
FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);


-- Tabla: reservas
CREATE TABLE reservas (
id INT AUTO_INCREMENT PRIMARY KEY,
huesped_id INT NOT NULL,
habitacion_id INT NOT NULL,
fecha_llegada DATE NOT NULL,
fecha_salida DATE NOT NULL,
precio_total DECIMAL(10,2) NOT NULL,
estado ENUM('Pendiente','Confirmada','Cancelada') NOT NULL DEFAULT 'Pendiente',
fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (huesped_id) REFERENCES huespedes(id) ON DELETE CASCADE,
FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);



-- Datos de ejemplo
INSERT INTO habitaciones (numero,tipo,precio_base,estado_limpieza) VALUES
('101','Sencilla',40.00,'Limpia'),
('102','Doble',60.00,'Sucia'),
('201','Suite',120.00,'Limpia');


INSERT INTO huespedes (nombre,email,documento_identidad) VALUES
('Juan Perez','juan@example.com','X1234567'),
('María López','maria@example.com','Y7654321');