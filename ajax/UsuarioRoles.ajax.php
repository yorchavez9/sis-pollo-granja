<?php

require_once "../controladores/Usuario.controlador.php";
require_once "../modelos/Usuario.modelo.php";

class AjaxUsuarioRoles
{


    /*=============================================
	EDITAR USUARIO ROLES
	=============================================*/

    public $idUsuario;

    public function ajaxEditarUsuarioRoles()
    {

        $item = "id_usuario";
        $valor = $this->idUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        echo json_encode($respuesta);
    }

    /*=============================================
	MOSTRAR DETALLE USUARIO ROLES
	=============================================*/

    public $idUsuarioVer;

    public function ajaxVerUsuarioRoles()
    {

        $item = "id_usuario";
        $valor = $this->idUsuarioVer;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        echo json_encode($respuesta);
    }

    /*=============================================
	ACTIVAR USUARIO ROLES
	=============================================*/

    public $activarUsuario;
    public $activarId;


    public function ajaxActivarUsuarioRoles()
    {

        $tabla = "usuarios";
        $item1 = "estado_usuario";
        $valor1 = $this->activarUsuario;

        $item2 = "id_usuario";
        $valor2 = $this->activarId;

        $respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);

        echo $respuesta;
    }

    /*=============================================
	VALIDAR NO REPETIR USUARIO ROLES
	=============================================*/

    public $validarUsuario;

    public function ajaxValidarUsuario()
    {

        $item = "usuario";
        $valor = $this->validarUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR USUARIO ROLES
=============================================*/
if (isset($_POST["idUsuario"])) {

    $editar = new AjaxUsuarioRoles();
    $editar->idUsuario = $_POST["idUsuario"];
    $editar->ajaxEditarUsuarioRoles();
}

/* VER DETALLE USUARIO ROLES */ 
elseif (isset($_POST["idUsuarioVer"])) {

    $verDetalle = new AjaxUsuarioRoles();
    $verDetalle->idUsuarioVer = $_POST["idUsuarioVer"];
    $verDetalle->ajaxVerUsuarioRoles();
}

/* ACTIVAR USUARIO ROLES */ 
elseif (isset($_POST["activarUsuario"])) {

    $activarUsuario = new AjaxUsuarioRoles();
    $activarUsuario->activarUsuario = $_POST["activarUsuario"];
    $activarUsuario->activarId = $_POST["activarId"];
    $activarUsuario->ajaxActivarUsuarioRoles();
}

/* VALIDAR USUARIO ROLES*/ 
elseif (isset($_POST["validarUsuario"])) {

    $valUsuario = new AjaxUsuarioRoles();
    $valUsuario->validarUsuario = $_POST["validarUsuario"];
    $valUsuario->ajaxValidarUsuario();
}

/* GUARDAR USUARIO ROLES */ 
elseif (isset($_POST["nombre_usuario"])) {

    $crearUsuario = new ControladorUsuarios();

    $crearUsuario->ctrCrearUsuario();
}

/* ACTUALIZAR USUARIO ROLES */ 
elseif (isset($_POST["edit_idUsuario"])) {

    $editusuario = new ControladorUsuarios();
    $editusuario->ctrEditarUsuario();
}

/* BORRAR USUARIO ROLES */ 
elseif (isset($_POST["deleteUserId"])) {

    $borrarUsuario = new ControladorUsuarios();
    $borrarUsuario->ctrBorrarUsuario();
}

/* MOSTRAR USUARIO ROLES EN LA TABLA  */ 
else {

    $item = null;
    $valor = null;
    $mostrarUsuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

    $tablaUsuarios = array();

    foreach ($mostrarUsuarios as $key => $usuario) {

        $fila = array(
            'id_usuario' => $usuario['id_usuario'],
            'id_sucursal' => $usuario['id_sucursal'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            'nombre_sucursal' => $usuario['nombre_sucursal'],
            'telefono' => $usuario['telefono'],
            'correo' => $usuario['correo'],
            'usuario' => $usuario['usuario'],
            'contrasena' => $usuario['contrasena'],
            'imagen_usuario' => $usuario['imagen_usuario'],
            'estado_usuario' => $usuario['estado_usuario'],
            'fecha_usuario' => $usuario['fecha_usuario']
        );


        $tablaUsuarios[] = $fila;
    }


    echo json_encode($tablaUsuarios);
}
