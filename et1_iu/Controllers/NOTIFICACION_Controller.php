<?php

include '../Models/NOTIFICACION_Model.php';
include '../Models/EMPLEADO_Model.php';
include '../Models/CLIENTE_Model.php';
include '../Models/ACTIVIDAD_Model.php';
include '../Locates/Strings_Castellano.php';
include '../Views/MENSAJE_Vista.php';


if (!IsAuthenticated()) {
    header('Location:../index.php');
}
include '../Locates/Strings_' . $_SESSION['IDIOMA'] . '.php';

//Genera los includes según las páginas a las que tiene acceso
$pags = generarIncludes();
for ($z = 0; $z < count($pags); $z++) {
    include $pags[$z];
}

function get_data_form() {
    //Recogemos el array de emails seleccionados y se lo mandamos al Modelo.

    if (!empty($_REQUEST['email']) && !isset($_REQUEST['NOTIFICACION_ASUNTO'])) {
        $NOTIFICACION_DESTINATARIOS = $_REQUEST['email'];
        return $NOTIFICACION_DESTINATARIOS;
    } elseif (isset($_REQUEST['actividad'])) {
        $ACTIVIDAD_ID = $_REQUEST['actividad'];
        $actividad = new actividad($ACTIVIDAD_ID, '', '', '', '', '');
        return $actividad;
    } else {
        $NOTIFICACION_REMITENTE = $_REQUEST['NOTIFICACION_REMITENTE'];
        $NOTIFICACION_PASSWORD = $_REQUEST['NOTIFICACION_PASSWORD'];
        $NOTIFICACION_NOMBRE_REMITENTE = $_REQUEST['NOTIFICACION_NOMBRE_REMITENTE'];
        $NOTIFICACION_DESTINATARIOS = $_REQUEST['NOTIFICACION_DESTINATARIOS'];
        $NOTIFICACION_ASUNTO = $_REQUEST['NOTIFICACION_ASUNTO'];
        $NOTIFICACION_CUERPO = $_REQUEST['NOTIFICACION_CUERPO'];

        $notificacion = new NOTIFICACION_Model($NOTIFICACION_REMITENTE, $NOTIFICACION_PASSWORD, $NOTIFICACION_NOMBRE_REMITENTE, $NOTIFICACION_DESTINATARIOS, $NOTIFICACION_ASUNTO, $NOTIFICACION_CUERPO);

        return $notificacion;
    }
}

//function get_data_form1() {
//
//    if (isset($_REQUEST['actividad'])) {
//        $ACTIVIDAD_ID = $_REQUEST['actividad'];
//        //echo $ACTIVIDAD_ID;
//        $actividad = new actividad($ACTIVIDAD_ID, '', '', '', '', '');
//    }
//
//    return $actividad;
//}

if (!isset($_REQUEST['accion'])) {
    $_REQUEST['accion'] = '';
}

Switch ($_REQUEST['accion']) {

    case $strings['Clientes']:  //Notificaciones sobre Clientes
        if (empty($_POST["email"])) {
            if (!tienePermisos('NOTIFICACION_USER_Select')) {
                new Mensaje('No tienes los permisos necesarios', 'NOTIFICACION_Controller.php');
            } else {
                $modelo = new CLIENTE_Modelo('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                $datos = $modelo->ConsultarTodo();
                new NOTIFICACION_USER_Select($datos, '../Controllers/NOTIFICACION_Controller.php', 'CLIENTE');
            }
        } else {
            $notificacion = get_data_form();
            new NOTIFICACION_EMAIL($notificacion, '../Controllers/NOTIFICACION_Controller.php?accion=', $strings['Clientes']);
        }
        break;

    case $strings['Empleados']: //Notificaciones sobre Empleados
        if (empty($_POST["email"])) {
            if (!tienePermisos('NOTIFICACION_USER_Select')) {
                new Mensaje('No tienes los permisos necesarios', 'NOTIFICACION_Controller.php');
            } else {
                $modelo = new EMPLEADOS_Modelo('', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
                $datos = $modelo->ConsultarTodo();
                new NOTIFICACION_USER_Select($datos, '../Controllers/NOTIFICACION_Controller.php', 'EMP');
            }
        } else {
            $notificacion = get_data_form();
            new NOTIFICACION_EMAIL($notificacion, '../Controllers/NOTIFICACION_Controller.php?accion=', $strings['Empleados']);
        }
        break;



    case $strings['Actividad']: //Notificacion sobre Clientes de una Actividad
        if (empty($_POST['email'])) {
            if (empty($_POST["actividad"])) {
                if (!tienePermisos('NOTIFICACION_ACTIVIDAD_Select')) {
                    new Mensaje('No tienes los permisos necesarios', 'NOTIFICACION_Controller.php');
                } else {
                    $modelo = new actividad('', '', '', '', '', '');
                    $datos = $modelo->ConsultarTodo();
                    new NOTIFICACION_ACTIVIDAD_Select($datos, '../Controllers/NOTIFICACION_Controller.php');
                }
            } else {
                //$actividad = get_data_form1();
                $actividad = get_data_form();
                $datos = $actividad->ConsultarClientesActividad();
                new NOTIFICACION_CLIENTE_ACTIVIDAD_Show($datos, '../Controllers/NOTIFICACION_Controller.php?accion=', $strings['Actividad']);
            }
        } else {
            $notificacion = get_data_form();
            new NOTIFICACION_EMAIL($notificacion, '../Controllers/NOTIFICACION_Controller.php?accion=', $strings['Actividad']);
        }
        break;



//         case $strings['Evento']: //Notificacion sobre Clientes de una Actividad
//        if (empty($_POST['email'])) {
//            if (empty($_POST["evento"])) {
//                if (!tienePermisos('NOTIFICACION_EVENTO_Select')) {
//                    new Mensaje('No tienes los permisos necesarios', 'NOTIFICACION_Controller.php');
//                } else {
//                    $modelo = new evento ('', '', '', '', '', '');
//                    $datos = $modelo->ConsultarTodo();
//                    new NOTIFICACION_EVENTO_Select($datos, '../Controllers/NOTIFICACION_Controller.php');
//                }
//            } else {
//                //$actividad = get_data_form1();
//                $actividad = get_data_form();
//                $datos = $actividad->ConsultarClientesActividad();
//                new NOTIFICACION_CLIENTE_EVENTO_Show($datos, '../Controllers/NOTIFICACION_Controller.php?accion=Actividad');
//            }
//        } else {
//            $notificacion = get_data_form();
//            new NOTIFICACION_EMAIL($notificacion, '../Controllers/NOTIFICACION_Controller.php?accion=Actividad');
//        }
//        break;


    case $strings['Enviar']: //Enviar
        if (isset($_REQUEST['NOTIFICACION_ASUNTO'])) {
            $notificacion = get_data_form();
            $respuesta = $notificacion->Enviar_Email();
            new Mensaje($respuesta, 'NOTIFICACION_Controller.php');
        }
        break;


    default:
        //La vista por defecto lista todas las páginas
        if (!tienePermisos('NOTIFICACION_Default')) {
            new Mensaje('No tienes los permisos necesarios', '../Views/DEFAULT_Vista.php');
        } else {
            new NOTIFICACION_Default('../Views/DEFAULT_Vista.php');
        }
}
?>
