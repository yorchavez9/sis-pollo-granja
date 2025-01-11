<?php

require_once "../controladores/Gastos.ingreso.controlador.php";
require_once "../controladores/Caja.general.controlador.php";
require_once "../modelos/Gastos.ingreso.modelo.php";
require_once "../modelos/Caja.general.modelo.php";


class AjaxGastoIngreso
{

    /*=============================================
	EDITAR GASTO INGRESO
	=============================================*/
    public $idGatosIngreso;
    public function ajaxEditarGatoIngreso()
    {
        $item = "id_gasto";
        $valor = $this->idGatosIngreso;
        $respuesta = ControladorGastoIngreso::ctrMostrarGastoIngreso($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR GASTO INGRESO
=============================================*/
if (isset($_POST["idGatosIngreso"])) {
    $editar = new AjaxGastoIngreso();
    $editar->idGatosIngreso = $_POST["idGatosIngreso"];
    $editar->ajaxEditarGatoIngreso();
}

//GUARDAR GASTO INGRESO
elseif (isset($_POST["id_usuario"])) {
    $crear = new ControladorGastoIngreso();
    $crear->ctrCrearGastoIngreso();
}

//ACTUALIZAR GASTO INGRESO
elseif(isset($_POST["id_gasto_edit"])){
    $editar = new ControladorGastoIngreso();
    $editar->ctrEditarGastoIngreso();
}

//ELIMINAR GASTO INGRESO
elseif(isset($_POST["idGatosIngresoDelete"])){
    $borrar = new ControladorGastoIngreso();
    $borrar->ctrBorraGastoIngreso();
}

//MOSTRAR GASTOS INGRESOS
else{
    $item = null;
    $valor = null;
    $mostrarDatos = ControladorGastoIngreso::ctrMostrarGastoIngreso($item, $valor);
    $tabla = array();
    foreach ($mostrarDatos as $key => $data) {
        $fila = array(
            'id_gasto' => $data['id_gasto'],
            'id_usuario' => $data['id_usuario'],
            'id_movimiento_caja' => $data['id_movimiento_caja'],
            'tipo' => $data['tipo'],
            'concepto' => $data['concepto'],
            'monto' => $data['monto'],
            'detalles' => $data['detalles'],
            'fecha' => $data['fecha']
        );
        $tabla[] = $fila;
    }
    
    echo json_encode($tabla);
}

