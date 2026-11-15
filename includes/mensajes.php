<?php

//lista de mensajes de error y exito
const MESSAGES = [

    //mensajes de error
    'error' => [
        '1' => 'No se pudo realizar la operacion.',
        'invalid_input' => 'Complete los datos correctamente.',
        'invalid_credentials' => 'Correo o contraseña incorrectos.',
        'login_required' => 'Debe iniciar sesion para continuar.',
        'session_expired' => 'La sesion expiro. Inicie sesion nuevamente.',
        'invalid_picture' => 'El archivo no es una imagen permitida.',
        'picture_too_large' => 'La imagen supera el tamaño permitido.',
        'upload_failed' => 'No se pudo guardar la imagen.',
        'codigo_duplicado' => 'Ya existe un equipo con ese codigo.',
        'correo_duplicado' => 'Ese correo ya esta registrado.',
        'categoria_duplicada' => 'Ya existe una categoria con ese nombre.',
        'prestamo' => 'prestamo no valido',
        'fecha_invalida' => 'Las fechas del prestamo no son validas.',
        'equipo_no_disponible' => 'Uno de los equipos seleccionados no esta disponible.',
        'plazo_excedido' => 'El prestamo supera los dias permitidos por la categoria.',
        'integridad' => 'No se pudo registrar el prestamo por un problema de integridad.',
        'estado_invalido' => 'No se puede realizar el cambio de estado del prestamo.',
        'estado_invalido' => 'No se puede realizar el cambio de estado del prestamo.',
        'devolucion_invalida' => 'El equipo no pertenece al prestamo o ya fue devuelto.',
        'prorroga_utilizada' => 'El prestamo ya utilizo su prorroga.',
        'prorroga_excedida' => 'La prorroga supera los dias permitidos.',
        'configuracion_invalida' => 'No se encontro una configuracion valida para la prorroga.',
    ],

    //mnsajes de exito
    'success' => [
        '1' => 'Operacion realizada correctamente.',
        'logged_out' => 'La sesion se cerro correctamente.',
    ],

    //Mensajes cuando se crea o elimina un registro
    'ok' => [
        '1' => 'Operacion realizada correctamente.',
    ],

    //mensajes cuando alguna imagen falla
    'parcial' => [
        '1' => 'La operacion se realizo, pero algunas imagenes no se pudieron guardar.',
    ],
];


//funciona para mostrar los mensajes en pantalla
function renderAlert()
{
    //recorre los tipos de mensajes disponibles
    foreach ([
        'error' => 'error',
        'success' => 'exito',
        'ok' => 'exito',
        'parcial' => 'parcial'
    ] as $tipo => $estilo) {

        //obtiene el codigo del mensaje desde la URL
        $codigo = $_GET[$tipo] ?? null;

        //comprueba que el codigo sea texto y exista en la lista
        if (is_string($codigo) && isset(MESSAGES[$tipo][$codigo])) {

            //obtiene el mensaje correspondiente
            $mensaje = MESSAGES[$tipo][$codigo];

            //evita que se interprete codigo HTML
            $mensaje = htmlspecialchars(
                $mensaje,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            //muestra el mensaje con nuestro CSS
            echo '<div class="mensaje mensaje-' . $estilo . '" role="alert">';

            echo $mensaje;

            echo '</div>';
        }
    }
}
