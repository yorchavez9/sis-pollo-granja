<?php

require_once "../controladores/Rol.controlador.php";
require_once "../modelos/Rol.modelo.php";

class AjaxRol
{
    /*=============================================
	EDITAR ROL
	=============================================*/
    public $idRol;
    public function ajaxEditarRol()
    {
        $item = "id_rol";
        $valor = $this->idRol;
        $respuesta = ControladorRol::ctrMostrarRoles($item, $valor);
        echo json_encode($respuesta);
    }

}

/*=============================================
EDITAR ROL
=============================================*/
if (isset($_POST["idRol"])) {
    $editar = new AjaxRol();
    $editar->idRol = $_POST["idRol"];
    $editar->ajaxEditarRol();
}

//GUARDAR ROL
elseif (isset($_POST["nombre_rol"])) {
    $crearRol = new ControladorRol();
    $crearRol->ctrCrearRol();
}

//ACTUALIZAR ROL
elseif (isset($_POST["edit_id_rol"])) {
    $editRol = new ControladorRol();
    $editRol->ctrEditarRol();
}

//ELIMINAR ROL
elseif (isset($_POST["delete_id_rol"])) {
    $borrarSucursal = new ControladorRol();
    $borrarSucursal->ctrBorraRol();
}

//MOSTRAR ROL
else {
    $item = null;
    $valor = null;
    $mostrarRoles = ControladorRol::ctrMostrarRoles($item, $valor);
    $tablaRol = array();
    foreach ($mostrarRoles as $key => $rol) {
        $fila = array(
            'id_rol' => $rol['id_rol'],
            'nombre_rol' => $rol['nombre'],
            'descripcion' => $rol['descripcion']
        );
        $tablaRol[] = $fila;
    }
    echo json_encode($tablaRol);
}
