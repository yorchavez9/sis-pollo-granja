<?php

require_once "../controladores/Pago.trabajador.controlador.php";
require_once "../modelos/Pago.trabajador.modelo.php";

if (
    (isset($_POST["filtro_estado_pago_t"]) && !empty($_POST["filtro_estado_pago_t"])) ||
    (isset($_POST["filtro_fecha_desde_pago_t"]) && !empty($_POST["filtro_fecha_desde_pago_t"])) ||
    (isset($_POST["filtro_fecha_hasta_pago_t"]) && !empty($_POST["filtro_fecha_hasta_pago_t"]))
) {

    $respuesta = ControladorPagos::ctrReportePagosTrabajador();
    echo json_encode($respuesta);
} else {
    $item = null;
    $valor = null;
    $mostrarPagos = ControladorPagos::ctrMostrarPagos($item, $valor);

    $tablaPagosReporte = array();

    foreach ($mostrarPagos as $key => $usuario) {

        $fila = array(
            'id_pagos' => $usuario['id_pagos'],
            'id_trabajador' => $usuario['id_trabajador'],
            'id_contrato' => $usuario['id_contrato'],
            'nombre' => $usuario['nombre'],
            'monto_pago' => $usuario['monto_pago'],
            'fecha_pago' => $usuario['fecha_pago'],
            'estado_pago' => $usuario['estado_pago']
        );


        $tablaPagosReporte[] = $fila;
    }


    echo json_encode($tablaPagosReporte);
}
