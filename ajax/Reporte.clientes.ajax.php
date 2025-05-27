<?php
require_once "../controladores/Cliente.controlador.php";
require_once "../modelos/Cliente.modelo.php";

header('Content-Type: application/json');

try {
    // Verificar si hay filtros enviados por POST
    $hasFilters = (
        (isset($_POST["id_cliente"]) && !empty($_POST["id_cliente"])) ||
        (isset($_POST["fecha_desde"]) && !empty($_POST["fecha_desde"])) ||
        (isset($_POST["fecha_hasta"]) && !empty($_POST["fecha_hasta"])) ||
        (isset($_POST["tipo_venta"]) && !empty($_POST["tipo_venta"])) ||
        (isset($_POST["estado_pago"]) && !empty($_POST["estado_pago"]))
    );

    if ($hasFilters) {
        // Obtener datos con filtros
        $respuesta = ControladorCliente::ctrMostrarReporteClientes();
    } else {
        // Obtener todos los datos
        $respuesta = ControladorCliente::ctrMostrarReporteClientesLista();
    }

    // Formatear respuesta
    $ventasFormateadas = array();
    foreach ($respuesta as $venta) {
        $ventasFormateadas[] = array(
            'id_venta' => $venta['id_venta'],
            'nombre_usuario' => $venta['nombre_usuario'] ?? 'N/A',
            'id_usuario' => $venta['id_usuario'],
            'id_persona' => $venta['id_persona'],
            'razon_social' => $venta['razon_social'] ?? 'Cliente no especificado',
            'numero_documento' => $venta['numero_documento'] ?? '',
            'direccion' => $venta['direccion'] ?? '',
            'telefono' => $venta['telefono'] ?? '',
            'email' => $venta['email'] ?? '',
            'tipo_comprobante_sn' => $venta['tipo_comprobante_sn'] ?? 'N/A',
            'serie_prefijo' => $venta['serie_prefijo'] ?? '000',
            'num_comprobante' => $venta['num_comprobante'] ?? '000000',
            'impuesto' => $venta['impuesto'] ?? 0,
            'tipo_pago' => $venta['tipo_pago'] ?? 'contado',
            'total_venta' => $venta['total_venta'] ?? 0,
            'sub_total' => $venta['sub_total'] ?? 0,
            'igv' => $venta['igv'] ?? 0,
            'total_pago' => $venta['total_pago'] ?? 0,
            'fecha_venta' => $venta['fecha_venta'] ?? null,
            'hora_venta' => $venta['hora_venta'] ?? null,
            'estado_pago' => $venta['estado_pago'] ?? 'pendiente'
        );
    }

    echo json_encode($ventasFormateadas);

} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ));
}