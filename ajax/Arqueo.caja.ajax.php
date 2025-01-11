<?php

require_once "../controladores/Arqueo.caja.controlador.php";
require_once "../modelos/Arqueo.caja.modelo.php";

class AjaxArqueoCaja
{

    /*=============================================
	EDITAR ARQUEO CAJA
	=============================================*/
    public $idArqueoCaja;
    public function ajaxEditarArqueoCaja()
    {
        $item = "id_arqueo";
        $valor = $this->idArqueoCaja;
        $respuesta = ControladorArqueoCaja::ctrMostrarArqueoCaja($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR ARQUEO CAJA
=============================================*/
if (isset($_POST["idArqueoCaja"])) {
    $editar = new AjaxArqueoCaja();
    $editar->idArqueoCaja = $_POST["idArqueoCaja"];
    $editar->ajaxEditarArqueoCaja();
}

//GUARDAR ARQUEO CAJA
elseif (isset($_POST["id_movimiento_caja"])) {
    $crear = new ControladorArqueoCaja();
    $crear->ctrCrearArqueoCaja();
}

//ACTUALIZAR ARQUEO CAJA
elseif(isset($_POST["id_arqueo_edit"])){
    $editar = new ControladorArqueoCaja();
    $editar->ctrEditarArqueoCaja();
}

//ELIMINAR ARQUEO CAJA
elseif(isset($_POST["idArqueoCajaDelete"])){
    $borrar = new ControladorArqueoCaja();
    $borrar->ctrBorraArqueoCaja();
}

//MOSTRAR ARQUEO CAJA
else{
    $item = null;
    $valor = null;
    $respuesta = ControladorArqueoCaja::ctrMostrarArqueoCaja($item, $valor);
    $tabla = array();
    foreach ($respuesta as $key => $data) {
        $fila = array(
            'id_arqueo' => $data['id_arqueo'],
            'id_movimiento_caja' => $data['id_movimiento_caja'],
            'id_usuario' => $data['id_usuario'],
            'fecha_arqueo' => $data['fecha_arqueo'],
            'monto_sistema' => $data['monto_sistema'],
            'monto_fisico' => $data['monto_fisico'],
            'diferencia' => $data['diferencia'],
            'observaciones' => $data['observaciones']
        );
        $tabla[] = $fila;
    }
    
    echo json_encode($tabla);
}

