<?php

require_once "../controladores/Gastos.ingreso.controlador.php";
require_once "../modelos/Gastos.ingreso.modelo.php";

if(
    (isset($_POST["id_usuario_reporte"]) && !empty($_POST["id_usuario_reporte"])) ||
    (isset($_POST["tipo_reporte"]) && ($_POST["tipo_reporte"] !== '')) ||
    (isset($_POST["fecha_desde_reporte"]) && !empty($_POST["fecha_desde_reporte"])) ||
    (isset($_POST["fecha_hasta_reporte"]) && !empty($_POST["fecha_hasta_reporte"]))
    ){

        $respuesta = ControladorGastoIngreso::ctrReporteGastosIngresos();
        echo json_encode($respuesta);

}else{
    $item = null;
    $valor = null;
    $response = ControladorGastoIngreso::ctrMostrarGastoIngreso($item, $valor);

    $tabla = array();

    foreach ($response as $key => $data) {

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
