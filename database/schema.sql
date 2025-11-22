CREATE DATABASE IF NOT EXISTS sportmarket_db;
USE sportmarket_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(128) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    nombre_completo VARCHAR(100),
    direccion TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen_url VARCHAR(255),
    categoria ENUM('Futbol', 'Basketball', 'F. Americano', 'Hockey', 'Baseball', 'Tenis') NOT NULL,
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    estado ENUM('disponible', 'vendido') DEFAULT 'disponible',
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    comprador_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado_envio ENUM('pendiente', 'enviado', 'entregado') DEFAULT 'pendiente',
    metodo_pago VARCHAR(50) DEFAULT 'tarjeta_simulada',
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (comprador_id) REFERENCES usuarios(id)
);

CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    producto_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);
