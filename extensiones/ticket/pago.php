<?php

// Incluyendo librerías necesarias
require "./code128.php";

session_start();

$nombre_usuario = $_SESSION["nombre_usuario"];

require_once "../../controladores/Historial.pago.controlador.php";
require_once "../../modelos/Historial.pago.modelo.php";

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

/* ========================================
MOSTRANDO DATOS DE LA VENTA
======================================== */
$item = "id_pago";
$valor = $_GET["id_pago"];

$respuesta = ControladorHistorialPago::ctrMostrarHistorialPagoPdf($item, $valor);

// Formateando la fecha de la venta
$fechaVenta = date("d/m/Y", strtotime($respuesta["fecha_registro"]));
$horaVenta = date("h:i A", strtotime($respuesta["fecha_registro"]));
$nombreCliente = $respuesta["razon_social"];
$montoTotal = $respuesta["total_venta"];
$montoPagado = $respuesta["monto_pago"]; // Asumiendo que este campo existe
$montoRestante = $respuesta["monto_restante"];


$tickets = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket(null, null);

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

    // Datos específicos del ticket
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . $fechaVenta . " " . $horaVenta), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Cliente: " . $nombreCliente), 0, 'C', false);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Monto Total: USD " . formatearPrecio($montoTotal))), 0, 'C', false);

    // Monto pagado y monto restante
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Monto Pagado: USD " . formatearPrecio($montoPagado)), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Monto Restante: USD " . formatearPrecio($montoRestante)), 0, 'C', false);

    // Finalizando ticket
    $pdf->Ln(10);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "-------------------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "Gracias por el pago"), 0, 0, 'C');
}

// Generando el PDF
if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    // Descargar el PDF
    $pdf->Output('D', 'ticket' . $respuesta["num_comprobante"] . '_v.pdf'); // 'D' fuerza la descarga con el nombre 'ticket.pdf'
} else {
    // Mostrar el PDF en el navegador (imprimir)
    $pdf->Output(); // Muestra el archivo en el navegador
}
