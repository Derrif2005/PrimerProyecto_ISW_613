create database prestamoutn;
use prestamoutn;

create table permisos(
    id int primary key auto_increment,
    clave varchar(50) not null unique
)ENGINE=InnoDB ;

create table roles(
    id int primary key auto_increment,
    nombre varchar(50) not null unique
)ENGINE=InnoDB ;


create table rol_permiso(
    rol_id int not null,
    permiso_id int not null,
    primary key(rol_id, permiso_id),
    foreign key (rol_id) references roles(id) on delete cascade,
    foreign key (permiso_id) references permisos(id) on delete cascade
)ENGINE=InnoDB ;



create table usuarios (
    id int primary key auto_increment,
    id_rol int not null,
    nombre varchar(50) not null,
    correo varchar(50) not null unique,
    password_hash varchar(255) not null,
    active boolean not null default true,  
    intentos INT DEFAULT 0,
    foreign key (id_rol) references roles(id) -- no le puse on delete cascade porque no quiero que se borre el usuario si se borra el rol
)ENGINE=InnoDB ;

ALTER TABLE usuarios
ADD COLUMN bloqueado DATETIME NULL DEFAULT NULL;


create table categorias(
    id int primary key auto_increment,
    nombre varchar(50) not null unique,
    dias_max_prestamo int not null
)ENGINE=InnoDB ;


ALTER TABLE categorias
ADD COLUMN active boolean not null default true;


create table equipos(
    id int primary key auto_increment,
    categoria_id int not null,
    nombre varchar(50) not null,
    codigo varchar(50) not null unique,
    active boolean not null default true,
    imagen_ruta VARCHAR(255) NULL,
    versionn VARCHAR(50) NULL DEFAULT NULL,
    eliminado_en DATETIME NULL DEFAULT NULL,
    foreign key (categoria_id) references categorias(id) on delete cascade
) ENGINE=InnoDB ;



-- tabla para almacenar varias imagenes
create table equipo_imagenes(
    id int primary key auto_increment,
    equipo_id int not null,
    imagen_ruta VARCHAR(255) not null,
    foreign key (equipo_id) references equipos(id) on delete cascade) ENGINE=InnoDB ;



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
)ENGINE=InnoDB ;

    ALTER TABLE prestamos
    ADD COLUMN prorrogas int not null default 0;


    create table configuracion(
    clave varchar(50) primary key,
    valor int not null
)ENGINE=InnoDB ;


create table prestamo_detalle(
    id int primary key auto_increment,
    prestamo_id int not null,
    equipo_id int not null,
    fecha_devolucion datetime null,
    unique(prestamo_id, equipo_id),

    foreign key (prestamo_id) references prestamos(id),
    foreign key (equipo_id) references equipos(id) 

)ENGINE=InnoDB ;


create table auth_tokens(
    id int primary key auto_increment,
    usuario_id int not null,
    selector varchar(18) not null unique,
    validator_hash varchar(64) not null,
    expira_en datetime not null,

    foreign key (usuario_id) references usuarios(id)
)ENGINE=InnoDB ;


create table bitacora(
    id int primary key auto_increment,
    usuario_id int not null,
    accion varchar(50) not null,
    entidad varchar(50) not null,
    entidad_id int not null,
    valores_antes json null,
    valores_despues json null,
    ip varchar(45) not null,
    creado_en datetime not null default current_timestamp,
    foreign key (usuario_id) references usuarios(id)
)ENGINE=InnoDB ;