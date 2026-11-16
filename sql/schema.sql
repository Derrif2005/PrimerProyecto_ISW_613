create database prestamoutn;

create table permisos(
    id int primary key auto_increment,
    clave varchar(50) not null unique
);

create table roles(
    id int primary key auto_increment,
    nombre varchar(50) not null unique
);


create table rol_permiso(
    rol_id int not null,
    permiso_id int not null,
    primary key(rol_id, permiso_id),
    foreign key (rol_id) references roles(id) on delete cascade,
    foreign key (permiso_id) references permisos(id) on delete cascade
);



create table usuarios (
    id int primary key auto_increment,
    id_rol int not null,
    nombre varchar(50) not null,
    correo varchar(50) not null unique,
    password_hash varchar(255) not null,
    active boolean not null default true,  
    intentos INT DEFAULT 0,
    foreign key (id_rol) references roles(id) -- no le puse on delete cascade porque no quiero que se borre el usuario si se borra el rol
);

ALTER TABLE usuarios
ADD COLUMN bloqueado DATETIME NULL DEFAULT NULL;


create table categorias(
    id int primary key auto_increment,
    nombre varchar(50) not null unique,
    dias_max_prestamo int not null
);


ALTER TABLE categorias
ADD COLUMN active boolean not null default true;


create table equipos(
    id int primary key auto_increment,
    categoria_id int not null,
    nombre varchar(50) not null,
    codigo varchar(50) not null unique,
    active boolean not null default true,
    imagen_ruta VARCHAR(255) NULL,
    versionn int not null default 1,
    eliminado_en DATETIME NULL DEFAULT NULL,
    foreign key (categoria_id) references categorias(id) on delete cascade
);



-- tabla para almacenar varias imagenes
create table equipo_imagenes(
    id int primary key auto_increment,
    equipo_id int not null,
    imagen_ruta VARCHAR(255) not null,
    foreign key (equipo_id) references equipos(id) on delete cascade);



create table prestamos(
    id int primary key auto_increment,
    usuario_id int not null,
    operador_id int not null,
    estado int not null default 1,
    fecha_inicio DATETIME not null,
    fecha_fin DATETIME not null,
    active boolean not null default true,

    foreign key (usuario_id) references usuarios(id),
    foreign key (operador_id) references usuarios(id)
);

    ALTER TABLE prestamos
    ADD COLUMN prorrogas int not null default 0;


    create table configuracion(
    clave varchar(50) primary key,
    valor int not null
);


create table prestamo_detalle(
    id int primary key auto_increment,
    prestamo_id int not null,
    equipo_id int not null,
    fecha_devolucion datetime null,
    unique(prestamo_id, equipo_id),

    foreign key (prestamo_id) references prestamos(id),
    foreign key (equipo_id) references equipos(id) 

);