<?php

require_once "../controladores/Caja.general.controlador.php";
require_once "../modelos/Caja.general.modelo.php";

$response = ControladorCajaGeneral::ctrMostrarEstadoIdCaja();
$table = array();
foreach ($response as $key => $data) {
    $fila = array(
        'id_movimiento' => $data['id_movimiento'],
        'fecha_apertura' => $data['fecha_apertura'],
        'estado' => $data['estado']
    );
    $table[] = $fila;
}

echo json_encode($table);
