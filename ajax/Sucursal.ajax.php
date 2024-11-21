<?php

require_once "../controladores/Sucursal.controlador.php";
require_once "../modelos/Sucursal.modelo.php";

class AjaxSucursal
{
    /*=============================================
	EDITAR CATEGORIA
	=============================================*/
    public $idSucursal;
    public function ajaxEditarSucursal()
    {
        $item = "id_sucursal";
        $valor = $this->idSucursal;
        $respuesta = ControladorSucursal::ctrMostrarSucursales($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
	ACTIVAR USUARIO
	=============================================*/

    public $activarSucursal;
    public $activarId;
    public function ajaxActivarSucursal()
    {
        $tabla = "sucursales";
        $item1 = "estado";
        $valor1 = $this->activarSucursal;
        $item2 = "id_sucursal";
        $valor2 = $this->activarId;
        $respuesta = ModeloSucursal::mdlActualizarSucursal($tabla, $item1, $valor1, $item2, $valor2);
    }

}

/*=============================================
EDITAR CATEGORIA
=============================================*/
if (isset($_POST["idSucursal"])) {
    $editar = new AjaxSucursal();
    $editar->idSucursal = $_POST["idSucursal"];
    $editar->ajaxEditarSucursal();
}
// ACTIVAR USUARIO 
elseif (isset($_POST["activarSucursal"])) {

    $activarSucursal = new AjaxSucursal();
    $activarSucursal->activarSucursal = $_POST["activarSucursal"];
    $activarSucursal->activarId = $_POST["activarId"];
    $activarSucursal->ajaxActivarSucursal();
}
//GUARDAR SUCURSAL
elseif (isset($_POST["nombre_sucursal"])) {
    $crearSucursal = new ControladorSucursal();
    $crearSucursal->ctrCrearSucursal();
}

//ACTUALIZAR SUCURSAL
elseif (isset($_POST["edit_id_sucursal"])) {
    $editSucursal = new ControladorSucursal();
    $editSucursal->ctrEditarSucursal();
}

//ELIMINAR SUCURSAL
elseif (isset($_POST["delete_id_sucursal"])) {
    $borrarSucursal = new ControladorSucursal();
    $borrarSucursal->ctrBorraSucursal();
}

//MOSTRAR SUCURSAL
else {
    $item = null;
    $valor = null;
    $mostrarSucursales = ControladorSucursal::ctrMostrarSucursales($item, $valor);
    $tablaSucursal = array();
    foreach ($mostrarSucursales as $key => $sucursal) {
        $fila = array(
            'id_sucursal' => $sucursal['id_sucursal'],
            'nombre_sucursal' => $sucursal['nombre_sucursal'],
            'direccion' => $sucursal['direccion'],
            'telefono' => $sucursal['telefono'],
            'estado' => $sucursal['estado']
        );
        $tablaSucursal[] = $fila;
    }
    echo json_encode($tablaSucursal);
}
