<?php

require_once "../controladores/Caja.general.controlador.php";
require_once "../modelos/Caja.general.modelo.php";

class AjaxCajaGeneral
{

    /*=============================================
	EDITAR CAJA GENERAL
	=============================================*/
    public $idCategoria;
    public function ajaxEditarCategoria()
    {
        $item = "id_categoria";
        $valor = $this->idCategoria;
        $respuesta = ControladorCajaGeneral::ctrMostrarCajaGeneal($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR CAJA GENERAL
=============================================*/
if (isset($_POST["idCategoria"])) {
    $editar = new AjaxCajaGeneral();
    $editar->idCategoria = $_POST["idCategoria"];
    $editar->ajaxEditarCategoria();
}

//GUARDAR CAJA GENERAL
elseif (isset($_POST["id_usuario_caja"])) {
    $crearAperturaCaja = new ControladorCajaGeneral();
    $crearAperturaCaja->ctrCrearCajaGeneral();
}

//ACTUALIZAR CAJA GENERAL
elseif (isset($_POST["id_movimiento_update"])) {
    $cierreCaja = new ControladorCajaGeneral();
    $cierreCaja->ctrEditarCajaGeneral();
}


elseif(isset($_POST["action"]) && $_POST["action"] == "reabrir_caja"){
    $datos = array(
        "id_movimiento" => $_POST["id_caja_update"],
        "estado" => $_POST["estado_update"]
    );
    
    $respuesta = ControladorCajaGeneral::ctrReabrirCajaGeneral($datos);
    echo json_encode($respuesta);
}

//MOSTRAR CAJA GENERAL
else {
    $item = null;
    $valor = null;
    $showCajaGeneral = ControladorCajaGeneral::ctrMostrarCajaGeneal($item, $valor);
    $tablaCajaGeneral = array();
    foreach ($showCajaGeneral as $key => $caja) {
        $fila = array(
            'id_movimiento' => $caja['id_movimiento'],
            'id_usuario' => $caja['id_usuario'],
            'tipo_movimiento' => $caja['tipo_movimiento'],
            'egresos' => $caja['egresos'],
            'ingresos' => $caja['ingresos'],
            'monto_inicial' => $caja['monto_inicial'],
            'monto_final' => $caja['monto_final'],
            'fecha_apertura' => $caja['fecha_apertura'],
            'fecha_cierre' => $caja['fecha_cierre'],
            'estado' => $caja['estado']
        );
        $tablaCajaGeneral[] = $fila;
    }

    echo json_encode($tablaCajaGeneral);
}
