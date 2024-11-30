<?php

require_once "../controladores/Ventas.controlador.php";
require_once "../modelos/Ventas.modelo.php";

if (
    (isset($_POST["filtro_usuario_venta"]) && !empty($_POST["filtro_usuario_venta"])) ||
    (isset($_POST["filtro_fecha_desde_venta"]) && !empty($_POST["filtro_fecha_desde_venta"])) ||
    (isset($_POST["filtro_fecha_hasta_venta"]) && !empty($_POST["filtro_fecha_hasta_venta"])) ||
    (isset($_POST["filtro_tipo_comprobante_venta"]) && !empty($_POST["filtro_tipo_comprobante_venta"])) ||
    (isset($_POST["filtro_estado_pago_venta"]) && !empty($_POST["filtro_estado_pago_venta"])) ||
    (isset($_POST["filtro_total_venta_min"]) && !empty($_POST["filtro_total_venta_min"])) ||
    (isset($_POST["filtro_total_venta_max"]) && !empty($_POST["filtro_total_venta_max"]))
) {
    $respuesta = ControladorVenta::ctrReporteVentas();
    echo json_encode($respuesta);
} else {
    $item = null;
    $valor = null;
    $mostrarVentas = ControladorVenta::ctrMostrarListaVentas($item, $valor);

    $tablaVentasReporte = array();

    foreach ($mostrarVentas as $key => $venta) {

        $fila = array(
            'id_venta' => $venta['id_venta'],
            'nombre_usuario' => $venta['nombre_usuario'],
            'id_usuario' => $venta['id_usuario'],
            'id_persona' => $venta['id_persona'],
            'razon_social' => $venta['razon_social'],
            'numero_documento' => $venta['numero_documento'],
            'direccion' => $venta['direccion'],
            'telefono' => $venta['telefono'],
            'email' => $venta['email'],
            'tipo_comprobante_sn' => $venta['tipo_comprobante_sn'],
            'serie_prefijo' => $venta['serie_prefijo'],
            'num_comprobante' => $venta['num_comprobante'],
            'impuesto' => $venta['impuesto'],
            'tipo_pago' => $venta['tipo_pago'],
            'total_venta' => $venta['total_venta'],
            'sub_total' => $venta['sub_total'],
            'igv' => $venta['igv'],
            'total_pago' => $venta['total_pago'],
            'fecha_venta' => $venta['fecha_venta'],
            'hora_venta' => $venta['hora_venta'],
            'estado_pago' => $venta['estado_pago']
        );


        $tablaVentasReporte[] = $fila;
    }


    echo json_encode($tablaVentasReporte);
}
