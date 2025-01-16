<?php

require_once "../controladores/Caja.general.controlador.php";
require_once "../modelos/Caja.general.modelo.php";

$response = ControladorCajaGeneral::ctrMostrarCalcularVentas();
$tabla = array();
foreach ($response as $key => $data) {
    $fila = array(
        'id_producto' => $data['id_producto'],
        'nombre_producto' => $data['nombre_producto'],
        'total_vendido' => $data['total_vendido'],
        'ganancia_por_unidad' => $data['ganancia_por_unidad'],
        'ganancia_total' => $data['ganancia_total']
    );
    $tabla[] = $fila;
}

echo json_encode($tabla);
