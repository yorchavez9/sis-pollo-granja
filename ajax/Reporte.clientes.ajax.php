<?php

require_once "../controladores/Cliente.controlador.php";
require_once "../modelos/Cliente.modelo.php";

if (
    (isset($_POST["id_cliente"]) && !empty($_POST["id_cliente"])) ||
    (isset($_POST["fecha_desde"]) && !empty($_POST["fecha_desde"])) ||
    (isset($_POST["fecha_hasta"]) && !empty($_POST["fecha_hasta"])) ||
    (isset($_POST["tipo_venta"]) && !empty($_POST["tipo_venta"]))
) {
    $respuesta = ControladorCliente::ctrMostrarReporteClientes();
    echo json_encode($respuesta);
} else {
    $mostrarVentas = ControladorCliente::ctrMostrarReporteClientesLista();

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
