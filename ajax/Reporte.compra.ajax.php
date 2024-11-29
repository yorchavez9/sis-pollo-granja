<?php

require_once "../controladores/Compra.controlador.php";
require_once "../modelos/Compra.modelo.php";

if (
    (isset($_POST["filtro_usuario_compra"]) && !empty($_POST["filtro_usuario_compra"])) ||
    (isset($_POST["filtro_fecha_desde_compra"]) && !empty($_POST["filtro_fecha_desde_compra"])) ||
    (isset($_POST["filtro_fecha_hasta_compra"]) && !empty($_POST["filtro_fecha_hasta_compra"])) ||
    (isset($_POST["filtro_tipo_comprobante_compra"]) && !empty($_POST["filtro_tipo_comprobante_compra"])) ||
    (isset($_POST["filtro_estado_pago_compra"]) && !empty($_POST["filtro_estado_pago_compra"])) ||
    (isset($_POST["filtro_total_compra_min"]) && !empty($_POST["filtro_total_compra_min"])) ||
    (isset($_POST["filtro_total_compra_max"]) && !empty($_POST["filtro_total_compra_max"]))
) {

    $respuesta = ControladorCompra::ctrReporteCompras();
    echo json_encode($respuesta);
} else {
    $item = null;
    $valor = null;
    $mostrarCompras = ControladorCompra::ctrMostrarCompras($item, $valor);

    $tablaProductos = array();

    foreach ($mostrarCompras as $key => $usuario) {

        $fila = array(
            'id_egreso' => $usuario['id_egreso'],
            'id_usuario' => $usuario['id_usuario'],
            'razon_social' => $usuario['razon_social'],
            'nombre_usuario' => $usuario['nombre_usuario'],
            'fecha_egre' => $usuario['fecha_egre'],
            'tipo_comprobante' => $usuario['tipo_comprobante'],
            'serie_comprobante' => $usuario['serie_comprobante'],
            'num_comprobante' => $usuario['num_comprobante'],
            'impuesto' => $usuario['impuesto'],
            'total_compra' => $usuario['total_compra'],
            'total_pago' => $usuario['total_pago'],
            'igv' => $usuario['igv'],
            'tipo_pago' => $usuario['tipo_pago'],
            'estado_pago' => $usuario['estado_pago']
        );


        $tablaProductos[] = $fila;
    }


    echo json_encode($tablaProductos);
}
