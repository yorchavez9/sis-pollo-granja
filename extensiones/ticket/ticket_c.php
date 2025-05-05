<?php

// Incluyendo librerías necesarias
require "./code128.php";

session_start();

$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];

require_once "../../controladores/Cotizacion.controllador.php";
require_once "../../modelos/Cotizacion.modelo.php";

require_once "../../controladores/Producto.controlador.php";
require_once "../../modelos/Producto.modelo.php";

require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

$pdf = new PDF_Code128('P', 'mm', array(80, 258));
$pdf->SetMargins(4, 10, 4);
$pdf->AddPage();

/* ========================================
FORMATEAR PRECIOS
======================================== */

function formatearPrecio($precio)
{
    return number_format($precio, 2, '.', ',');
}

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

/* ========================================
MOSTRANDO DATOS DE LA VENTA
======================================== */
$item = "id_cotizacion";
$valor = $_GET["id_cotizacion"];

$respuesta = ControladorCotizacion::ctrMostrarListaCotizaciones($item, $valor);

$respuesta_de = ControladorCotizacion::ctrMostrarDetalleCotizacion($item, $valor);
$fechaVenta = date("d/m/Y", strtotime($respuesta["fecha_cotizacion"]));
$numeroCotizacion = $respuesta["id_cotizacion"];
$itemConfig = null;
$valorConfig = null;

$tickets = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($itemConfig, $valorConfig);

foreach ($tickets as $ticket) {

    // Encabezado y datos de la empresa
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper($ticket["nombre_empresa"])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "RUC: " . $ticket["ruc"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $ticket["direccion"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $ticket["telefono"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: " . $ticket["correo"] . ""), 0, 'C', false);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . $fechaVenta . " " . $respuesta["hora_cotizacion"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Emitido por: " . $nombre_usuario . ""), 0, 'C', false);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Ticket N°: " . $respuesta["id_cotizacion"] . "")), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cliente: " . $respuesta["razon_social"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Documento: " . $respuesta["numero_documento"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $respuesta["telefono"] . ""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $respuesta["direccion"] . ""), 0, 'C', false);

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

    /* Detalles de la tabla */

    $subtotal = 0;

    foreach ($respuesta_de as $value) {
        $totalPrecioProducto = $value["peso_neto"] * $value["precio_venta"];
        $subtotal += $totalPrecioProducto;

        $pdf->MultiCell(0, 2, iconv("UTF-8", "ISO-8859-1", ""), 0, 'C'); // Si no necesitas esta línea, puedes eliminarla

        $pdf->MultiCell(0, 4, iconv("UTF-8", "ISO-8859-1", "" . $value["nombre_producto"] . ""), 0, 'C', false); // Esto puede quedarse si necesitas el nombre del producto

        // Ajustando los tamaños de las celdas
        $pdf->Cell(5, 4, iconv("UTF-8", "ISO-8859-1", "" . $value["numero_javas"] . ""), 0, 0, 'C');
        $pdf->Cell(15, 4, iconv("UTF-8", "ISO-8859-1", "" . $value["numero_aves"] . ""), 0, 0, 'C');
        $pdf->Cell(18, 4, iconv("UTF-8", "ISO-8859-1", "" . $value["peso_neto"] . ""), 0, 0, 'C');
        $pdf->Cell(14, 4, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($value["precio_venta"]) . ""), 0, 0, 'C');
        $pdf->Cell(24, 4, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($totalPrecioProducto) . ""), 0, 0, 'C');

        $pdf->Ln(5);
    }

    $sub_total_bolivares = $respuesta["sub_total"] * $currentRate;
    $impuesto_bolivares = $respuesta["igv_total"] * $currentRate;
    $total_bolivares = $respuesta["total_cotizacion"] * $currentRate;

    /* Fin detalles de la tabla */

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "SUBTOTAL"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($respuesta["sub_total"]) . ""), 0, 0, 'R');

    $pdf->Ln(5);

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "USD " . formatearPrecio($sub_total_bolivares) . ""), 0, 0, 'R');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "IVG (" . intval($respuesta["impuesto"]) . " %):"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($respuesta["igv_total"]) . ""), 0, 0, 'R');
    
    $pdf->Ln(5);

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "USD " . formatearPrecio($impuesto_bolivares) . ""), 0, 0, 'R');

    $pdf->Ln(5);

    $pdf->Cell(18, 5, iconv("UTF-8", "ISO-8859-1", ""), 0, 0, 'C');
    $pdf->Cell(22, 5, iconv("UTF-8", "ISO-8859-1", "TOTAL A PAGAR"), 0, 0, 'R');
    $pdf->Cell(32, 5, iconv("UTF-8", "ISO-8859-1", "S/ " . formatearPrecio($respuesta["total_cotizacion"]) . ""), 0, 0, 'R');

    $pdf->Ln(5);

    $pdf->Cell(72, 5, iconv("UTF-8", "ISO-8859-1", "USD " . formatearPrecio($total_bolivares) . ""), 0, 0, 'R');

    $pdf->Ln(10);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');

    $pdf->Ln(5);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", $ticket["mensaje"]), 0, 0, 'C');
}

/* ======================================================
GUARDANDO EL COMPROBANTE EN EL DIRECTORIO DEL TCIKET
====================================================== */
$directorio = "ticket/cotizacion/";
$nombreArchivo = 'ticket_c_' . $numeroCotizacion . '.pdf';
$rutaArchivo = $directorio . $nombreArchivo;
if (!file_exists($rutaArchivo)) {
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $pdf->Output('F', $rutaArchivo);
} else {
    echo "El archivo PDF ya existe.";
}
