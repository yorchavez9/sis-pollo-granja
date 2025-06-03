<?php
// Incluyendo librerías necesarias
require '../ticket/code128.php';
session_start();

$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];

require_once "../../controladores/Compra.controlador.php";
require_once "../../controladores/Producto.controlador.php";
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Compra.modelo.php";
require_once "../../modelos/Producto.modelo.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

// Formatear precios
function formatearPrecio($precio) {
    return number_format($precio, 2, '.', ',');
}

// Obtener tipo de cambio
function getExchangeRate() {
    $primaryUrl = 'https://api.exchangerate-api.com/v4/latest/PEN';
    $backupUrl = 'https://open.er-api.com/v6/latest/PEN';

    $response = file_get_contents($primaryUrl);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['rates']['USD'])) {
            return $data['rates']['USD'];
        }
    }

    $responseBackup = file_get_contents($backupUrl);
    if ($responseBackup !== false) {
        $dataBackup = json_decode($responseBackup, true);
        if (isset($dataBackup['rates']['USD'])) {
            return $dataBackup['rates']['USD'];
        }
    }

    return null;
}
$currentRate = getExchangeRate();

// Inicializar PDF
$pdf = new PDF_Code128('P', 'mm', array(80, 258));
$pdf->SetMargins(4, 10, 4);
$pdf->AddPage();

// Mostrando datos de la compra
$item = "id_egreso";
$valor = $_GET["id_egreso"];

$respuesta = ControladorCompra::ctrMostrarCompras($item, $valor);
$respuesta_de = ControladorCompra::ctrMostrarDetalleCompra($item, $valor);
$fechaEgreso = date("d/m/Y", strtotime($respuesta["fecha_egre"]));
$serie_comprobante = $respuesta["serie_comprobante"];
$numero_comprobante = $respuesta["num_comprobante"];

// Obtener configuración de la empresa
$itemConfig = null;
$valorConfig = null;
$tickets = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($itemConfig, $valorConfig);

foreach ($tickets as $ticket) {
    // Encabezado y datos de la empresa
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper($ticket["nombre_empresa"])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "RUC: " . $ticket["ruc"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $ticket["direccion"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $ticket["telefono"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: " . $ticket["correo"]), 0, 'C', false);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . $fechaEgreso . " " . $respuesta["hora_egreso"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Emitido por: " . $nombre_usuario), 0, 'C', false);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Factura N°: " . $respuesta["serie_comprobante"] . '-' . $respuesta["num_comprobante"])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    // Información del proveedor
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Proveedor: " . $respuesta["razon_social"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Documento: " . $respuesta["numero_documento"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $respuesta["telefono"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $respuesta["direccion"]), 0, 'C', false);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(3);

    // Tabla de productos
    $pdf->Cell(15, 5, iconv("UTF-8", "ISO-8859-1", "Javas"), 0, 0, 'L');
    $pdf->Cell(15, 5, iconv("UTF-8", "ISO-8859-1", "Uni."), 0, 0, 'L');
    $pdf->Cell(14, 5, iconv("UTF-8", "ISO-8859-1", "Peso"), 0, 0, 'L');
    $pdf->Cell(10, 5, iconv("UTF-8", "ISO-8859-1", "Precio"), 0, 0, 'L');
    $pdf->Cell(15, 5, iconv("UTF-8", "ISO-8859-1", "Total"), 0, 0, 'R');

    $pdf->Ln(3);
    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(3);

    // Detalles de la tabla
    $subtotal = 0;
    usort($respuesta_de, function($a, $b) { return strcmp($a['nombre_producto'], $b['nombre_producto']); });
    foreach ($respuesta_de as $value) {
        $totalPrecioProducto = ($value['peso_neto'] == 0) ? $value['numero_aves'] * $value['precio_compra'] : $value['peso_neto'] * $value['precio_compra'];
        $subtotal += $totalPrecioProducto;

        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", $value["nombre_producto"]), 0, 'C', false);
        $pdf->Cell(5, 4, iconv("UTF-8", "ISO-8859-1", intval($value["numero_javas"])), 0, 0, 'C');
        $pdf->Cell(15, 4, iconv("UTF-8", "ISO-8859-1", $value["numero_aves"]), 0, 0, 'C');
        $pdf->Cell(18, 4, iconv("UTF-8", "ISO-8859-1", $value["peso_neto"]), 0, 0, 'C');
        $pdf->Cell(14, 4, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($value["precio_compra"])), 0, 0, 'C');
        $pdf->Cell(24, 4, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($totalPrecioProducto)), 0, 0, 'C');
        $pdf->Ln(5);
    }

    // Totales
    $impuestoTotal = $subtotal * ($respuesta["impuesto"] / 100);
    $totalConImpuesto = $subtotal + $impuestoTotal;

    $sub_total_bolivares = $subtotal * $currentRate;
    $impuesto_bolivares = $impuestoTotal * $currentRate;
    $total_bolivares = $totalConImpuesto * $currentRate;

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "SUBTOTAL"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($subtotal)), 0, 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "$ " . formatearPrecio($sub_total_bolivares)), 0, 0, 'R');
    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "IGV (" . intval($respuesta["impuesto"]) . " %):"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($impuestoTotal)), 0, 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "$ " . formatearPrecio($impuesto_bolivares)), 0, 0, 'R');
    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($totalConImpuesto)), 0, 0, 'R');
    $pdf->Ln(5);
    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "$ " . formatearPrecio($total_bolivares)), 0, 0, 'R');

    // Mostrar saldo restante si es crédito
    if ($respuesta["tipo_pago"] == "credito") {
        $saldoRestante = $totalConImpuesto - $respuesta["total_pago"];
        $totalPagado = 0;
        $textoPagado = '';
        if ($respuesta["pago_delante"] == $respuesta["total_pago"]) {
            $totalPagado = $respuesta["pago_delante"];
            $textoPagado = 'Pago inicial:';
        } else {
            $totalPagado = $respuesta["total_pago"];
            $textoPagado = 'Total pagado:';
        }

        $pdf->Ln(5);
        $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
        $pdf->Ln(5);

        $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
        $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", $textoPagado), 0, 0, 'R');
        $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($totalPagado)), 0, 0, 'R');

        $pdf->Ln(5);
        $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
        $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "Saldo pendiente:"), 0, 0, 'R');
        $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($saldoRestante)), 0, 0, 'R');
    }

    $pdf->Ln(10);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", $ticket["mensaje"]), 0, 0, 'C');
}

// Generando el PDF
if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    $pdf->Output('D', 'factura_compra_' . $numero_comprobante . '.pdf');
} else {
    $pdf->Output();
}
?>