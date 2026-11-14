INSERT INTO permisos (clave) VALUES
--Permisos del administrador
('usuarios_ver'),
('usuarios_crear'),
('equipos_crear'),
('reportes_ver'),

--Permisos de ambos
('equipos_ver'),

--Permisos del operador
('prestamos_ver'),
('prestamos_crear');


INSERT INTO roles (nombre) VALUES
('administrador'),
('operador'),
('profesor');

INSERT INTO rol_permiso (rol_id, permiso_id) VALUES
--Permisos del administrador
(1, 1),-- usuarios_ver
(1, 2),-- usuarios_crear
(1, 3),-- equipos_crear
(1, 4),-- reportes_ver
(1, 5),-- equipos_ver
--Permisos del operador
(2, 5),-- equipos_ver
(2, 6),-- prestamos_ver
(2, 7);-- prestamos_crear

INSERT into usuarios (id_rol, nombre, correo, password_hash) VALUES
(1, 'admin', 'admin@prestamoUTN.com', '$2y$10$sfOHyTYinozUSGrxpiN5..IcOej1hGvsdX7K987Ll2DffYtYtR096'); -- contraseña: 12345678


INSERT INTO categorias (nombre, dias_max_prestamo) VALUES
('Computadoras portatiles', 3),
('Proyector', 1),
('tablet', 2);


INSERT INTO equipos (categoria_id, nombre, codigo) VALUES
(1, 'Laptop HP ProBook 450 G9', '801581'),
(1, 'Laptop Lenovo ThinkPad E14', '801582'),
(1, 'Laptop ASUS VivoBook 15', '801583'),
(1, 'Laptop Acer Aspire 5', '801584'),
(1, 'Laptop Dell Latitude 5420', '801585'),
(1, 'Laptop HP EliteBook 840 G8', '801586'),
(1, 'Laptop Lenovo IdeaPad 3', '801587'),
(1, 'Laptop ASUS ExpertBook B1', '801588'),
(1, 'Laptop Dell Vostro 3510', '801589'),
(1, 'Laptop Acer TravelMate P2', '801590'),
(1, 'Laptop MSI Modern 15', '801591'),
(1, 'Laptop HP Pavilion 15', '801592'),
(1, 'Laptop Lenovo ThinkBook 15', '801593'),
(1, 'Laptop Dell Inspiron 14', '801594'),
(1, 'Laptop ASUS ZenBook 14', '801595'),
(1, 'Laptop Acer Swift 3', '801596'),
(1, 'Laptop HP Victus 15', '801597'),
(1, 'Laptop Lenovo Legion 5', '801598'),
(1, 'Laptop ASUS TUF Gaming A15', '801599'),
(1, 'Laptop Dell G15 Gaming', '801600'),
(1, 'Laptop MSI Katana 15', '801601'),
(1, 'Laptop HP Omen 16', '801602'),
(1, 'Laptop Lenovo LOQ 15', '801603'),
(1, 'Laptop ASUS ROG Strix G16', '801604'),
(1, 'Laptop Acer Nitro V15', '801605');


