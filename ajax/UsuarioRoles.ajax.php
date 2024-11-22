<?php

require_once "../controladores/UsuarioRoles.controlador.php";
require_once "../modelos/UsuarioRoles.modelo.php";

class AjaxUsuarioRoles
{


    /*=============================================
	EDITAR USUARIO ROLES
	=============================================*/

    public $idUsuarioRol;

    public function ajaxEditarUsuarioRoles()
    {

        $item = "id_usuario";
        $valor = $this->idUsuarioRol;

        $respuesta = ControladorUsuarioRoles::ctrMostrarUsuarioRoles($item, $valor);

        echo json_encode($respuesta);
    }


}

/*=============================================
EDITAR USUARIO ROLES
=============================================*/
if (isset($_POST["idUsuarioRol"])) {

    $editar = new AjaxUsuarioRoles();
    $editar->idUsuarioRol = $_POST["idUsuarioRol"];
    $editar->ajaxEditarUsuarioRoles();
}


/* GUARDAR USUARIO ROLES */ 
elseif (isset($_POST["id_usuario_roles"])) {
    $crear_usuario_roles = new ControladorUsuarioRoles();
    $crear_usuario_roles->ctrCrearUsuarioRoles();
}

/* ACTUALIZAR USUARIO ROLES */ 
elseif (isset($_POST["edit_id_usuario_roles"])) {

    $editusuarioRoles = new ControladorUsuarioRoles();
    $editusuarioRoles->ctrEditarUsuarioRoles();
}

/* BORRAR USUARIO ROLES */ 
elseif (isset($_POST["deleteIdUsuarioRol"])) {

    $borrarUsuario = new ControladorUsuarioRoles();
    $borrarUsuario->ctrBorrarUsuarioRoles();
}

/* MOSTRAR USUARIO ROLES EN LA TABLA  */ 
else {

    $item = null;
    $valor = null;
    $mostrarUsuarioRoles = ControladorUsuarioRoles::ctrMostrarUsuarioRoles($item, $valor);

    $tablaUsuarioRoles = array();

    foreach ($mostrarUsuarioRoles as $key => $usuario_rol) {

        $fila = array(
            'id_usuario' => $usuario_rol['id_usuario'],
            'nombre_usuario' => $usuario_rol['nombre_usuario'],
            'nombre_rol' => $usuario_rol['nombre_rol']
        );


        $tablaUsuarioRoles[] = $fila;
    }


    echo json_encode($tablaUsuarioRoles);
}
