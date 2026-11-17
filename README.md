# Universidad Técnica Nacional

## Ingeniería del Software

### Programación en Ambiente Web I

#### Proyecto programado 1

## 1. Instalación desde cero

### 1.1 Requisitos
Aplicaciones necesarias para el funcionamiento del proyecto
1. XAMPP
2. PHP 8
3. MySQL
4. Apache
5. Git
6. Navegador web(A su gusto)
7. Visual Studio Code

### 1.2 Configuracion del virtualHost

Para configurar el vitual Host necesitas entrar ala siguiente ruta, lo puedes hacer desde el windows + r o 

C:\xampp\apache\conf\extra\httpd-vhosts.conf

y agregar la siguientes lineas de codidgo 
<VirtualHost *:80>
    ServerName prestamoutn.local.com
    DocumentRoot "C:/Users/Derri/PrimerProyecto_ISW_613/public"

    <Directory "C:/Users/Derri/PrimerProyecto_ISW_613/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/prestamoutn-error.log"
    CustomLog "logs/prestamoutn-access.log" common
</VirtualHost>

### 1.3 Configuracion del archivo host

Abre el bloc de notas como administrador y ejecuta el archivo ubicado en:

C:\Windows\System32\drivers\etc\hosts

y agrega al final lo siguiente 

127.0.0.1 prestamoutn.local.com

Guarda los cambios desde el menu y reinicia Apache en XAMPP

### 1.4 ¿Como clonar el repositorio?

Los pasos a tomar para clonar el repositorio son los siguientes:

1. -> Abre Git bash o el de tu preferencia 

Mediante el comando cd entras a la carpeta que quieres entrar ejemplo: "cd /c/user/TU_CARPETA"

luego ejecuta 

git clone https://github.com/Derrif2005 PrimerProyecto_ISW_613.git

y ingresa ala carpeta "PrimerProyecto_ISW_613" con cd 

### 1. Configuración de XAMPP

1. Abrir el panel de control de XAMPP

2. Iniciar el servicio Apache mediante el botón Start

3. Iniciar el servicio MySQL mediante el botón Start

4. Comprobar que ambos servicios se encuentren en ejecución

5. Ingresar a http://localhost/phpmyadmin para  administrar la base de datos

## 2. Manual de uso
A continuación explicare el uso del sistema

## 1. Inicio de sesión
En el inicio de sesión consta de tres pasos para inicar sesion

Lo primero a mencionar es tener un usuario previo para el inicio de sesión

Van a ver 5 campos interatubles que vamos a usar como:

"Correo"

"Contraseña"

"Recordar"

"Iniciar Sesion"

"Registrarse"

### 1.1 Recordarme
Esta check lo que hara es recordar tu usuario creando una cookie

## 2. Manual del administrador
Cuando inicies sesion podras utilizar 2 tipos de usuarios, por ahora explicare el usuario administrador y sus funciones.

### 2.1 Gestión de usuarios
Este usuario podra utilizar la Pagina(CRUD) de gestionar usuarios, donde podra utilizar multiples funciones como:

Filtro por nombre:
Este filtro mostrara las nombres de los usuarios que estemos buscando 

Tambien podemos seleccionar la cantidad de usuarios a mostrar en la tabla y confirmamos la cantidad con el boton seleccionar 

Crear usuario:
Podemos crear nuevos usuarios ingresando el nombre, correo, contraseña y seleccionando el tipo de rol que queremos asignarle al usuario

Actualizar usuario:
Podemos buscar el usuario por medio del correo y una vez encontrado podremos modificar los datos del usuario, como el nombre, correo, rol y su estado

Eliminar usuario:
Podemos eliminar un usuario por medio del boton eliminar, esto no eliminara directamente al usuario de la base de datos, solamente cambiara su estado a inactivo y no se mostrara en la tabla

### 2.2 Gestión de categorías
En esta Pagina (CRUD) podremos gestionar las categorias que utilizaremos para clasificar los equipos

Filtro por nombre:
Podemos buscar las categorias por medio del nombre y tambien podemos seleccionar la cantidad de categorias que queremos mostrar en la tabla

Crear categoria:
Podemos crear una nueva categoria ingresando los datos que solicita el formulario

Actualizar categoria:
Podemos buscar una categoria que ya existe y modificar los datos que fueron ingresados anteriormente

Eliminar categoria:
Podemos eliminar una categoria utilizando el boton eliminar, la categoria cambiara su estado a inactivo y no se mostrara en la tabla

### 2.3 Gestión de equipos
En esta Pagina (CRUD) podremos gestionar los equipos que posteriormente seran utilizados para realizar los prestamos

Filtro por nombre:
Este filtro mostrara los equipos que estemos buscando por medio del nombre, tambien podemos seleccionar la cantidad de equipos que queremos mostrar en la tabla

Crear equipo:
Para crear un equipo debemos seleccionar la categoria a la que pertenece, ingresar el nombre, codigo y seleccionar las imagenes del equipo

Podemos seleccionar multiples imagenes para un mismo equipo

Actualizar equipo:
Podemos buscar un equipo por medio del nombre o utilizando el boton editar que aparece en la tabla, una vez encontrado podremos modificar los datos del equipo y tambien agregar nuevas imagenes

Eliminar equipo:
Podemos eliminar un equipo utilizando el boton eliminar, esto cambiara el estado del equipo a inactivo y no se mostrara en la tabla

### 2.4 Reportes y exportación CSV

Esta Pagina permite al usuario administrador consultar cuales son los equipos mas prestados dentro de un rango de fechas

Para realizar esta consulta debemos seleccionar una fecha de inicio y una fecha de fin y realizar la busqueda, esto nos mostrara los equipos y la cantidad de veces que fueron prestados

Tambien podemos descargar los datos del reporte por medio del boton de exportar CSV, este archivo podra ser abierto con aplicaciflowchart TD
    A[Recibir datos del préstamo] --> B[Validar fechas y equipos]
    B --> C[Iniciar transacción]
    C --> D[Bloquear equipos con FOR UPDATE]
    D --> E{¿Existe traslape?}
    E -->|Sí| F[rollBack]
    E -->|No| G[Registrar préstamo]
    G --> H[Registrar detalles]
    H --> I[Registrar bitácora]
    I --> J[commit]
    class F red
    class J greenones como Excel

## 3. Manual del operador
Ahora explicare las funciones que podra utilizar el usuario operador, este usuario se encargara principalmente de gestionar los prestamos y consultar los equipos que estan registrados en el sistema

El usuario operador no podra crear, actualizar o eliminar usuarios, categorias y equipos

### 3.1 Consulta de equipos

El operador podra ingresar a la Pagina de equipos donde podra visualizar los equipos registrados, sus nombres, categorias, codigos e imagenes

Tambien podra utilizar el filtro por nombre y seleccionar la cantidad de equipos que quiere mostrar en la tabla

A diferencia del usuario administrador este usuario solamente podra consultar los equipos y no podra modificar los datos

### 3.2 Equipos consultados recientemente

Cuando el operador realiza una consulta de un equipo, el sistema guardara el identificador del equipo por medio de una cookie

Esta cookie podra guardar hasta 5 equipos consultados recientemente, esto permite al usuario seleccionar un equipo que ya habia consultado anteriormente sin tener que escribir nuevamente el nombre del equipo

### 3.3 Creación de préstamos

Para realizar un prestamo debemos ingresar a la Pagina de prestamos, donde podremos seleccionar el usuario al que se le realizara el prestamo, los equipos que desea solicitar, la fecha de inicio y la fecha de fin del prestamo

Tambien podemos seleccionar multiples equipos para realizar un solo prestamo
El sistema comprobara que los equipos no tengan otro prestamo activo que coincida con las fechas que estamos seleccionando, esto para evitar que un mismo equipo sea prestado a diferentes usuarios durante las mismas fechas

Cuando se crea un prestamo su estado inicial sera solicitado

### 3.4 Estados de los préstamos

Los prestamos cuentan con 5 tipos de estados, los cuales nos permiten conocer en que parte del proceso se encuentra el prestamo

Solicitado: Es el estado que tendra el prestamo cuando se crea

Aprobado: Indica que el prestamo fue aprobado

Entregado: Indica que los equipos fueron entregados al usuario

Devuelto: Indica que todos los equipos del prestamo fueron devueltos

Cancelado: Indica que el prestamo fue cancelado

El sistema comprobara que los cambios de estado sean permitidos dependiendo del estado actual del prestamo

### 3.5 Devolución parcial y completa

Cuando un prestamo se encuentra en estado entregado podemos realizar la devolucion de los equipos

Devolucion parcial:
Podemos devolver uno o varios equipos sin necesidad de devolver todos los equipos que pertenecen al prestamo, los equipos que todavia no fueron devueltos continuaran pendientes de devolucion

Devolucion completa:
Cuando todos los equipos que pertenecen al prestamo fueron devueltos, el prestamo cambiara su estado a devuelto

### 3.6 Prórroga de préstamos

Esta funcion permite modificar la fecha de fin de un prestamo para que el usuario pueda utilizar los equipos durante mas tiempo

Para realizar una prorroga debemos tener en cuenta las siguientes condiciones:

El prestamo debe estar en estado entregado

Solamente podemos realizar una prorroga por prestamo

La prorroga no puede superar la cantidad de dias permitidos por el sistema

Las nuevas fechas no pueden generar un traslape con otros prestamos activos de los equipos

Si alguna de estas condiciones no se cumple el sistema no permitira realizar la prorroga y mostrara un mensaje de error

## 4. Funciones generales

Estas son algunas de las funciones que podremos encontrar en diferentes Paginas del sistema y que nos permiten utilizarlo de una manera mas sencilla

### 4.1 Búsqueda y paginación

En las diferentes Paginas (CRUD) podemos utilizar un filtro por nombre, esto nos permite encontrar los registros que estamos buscando sin necesidad de revisar todos los datos de la tabla

Tambien podemos seleccionar entre 5, 10 y 20 registros para mostrar en la tabla, una vez seleccionada la cantidad el sistema guardara este valor por medio de una cookie, de manera que cuando volvamos a ingresar a la Pagina se utilizara la cantidad que seleccionamos anteriormente

Podemos utilizar los botones anterior y siguiente para desplazarnos entre las diferentes paginas de la tabla

### 4.2 Mensajes del sistema

El sistema mostrara diferentes mensajes dependiendo de las acciones que realicemos

Cuando realizamos una accion correctamente se mostrara un mensaje de exito, pero si ocurre algun problema con los datos ingresados o intentamos realizar una accion que no esta permitida, el sistema mostrara un mensaje de error

Esto nos permite conocer si las acciones que estamos realizando fueron ejecutadas correctamente

### 4.3 Cierre de sesión

Para cerrar la sesion debemos seleccionar el boton Cerrar sesion que se encuentra en la barra de navegacion

Cuando realizamos esta accion el sistema eliminara la sesion del usuario y en caso de que se haya utilizado la opcion Recordarme tambien eliminara el token de la base de datos y su respectiva cookie

Una vez cerrada la sesion tendremos que ingresar nuevamente el correo y la contraseña para utilizar el sistema

## 3. Explicación de la lógica

Ahora explicare la logica del proyecto.

Para que el proyecto pueda empezar a cagar necesitas

/config/Conexion.php

Esta se maneja de la siguiente manera 





## 4. Seguridad del sistema