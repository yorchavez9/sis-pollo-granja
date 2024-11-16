<?php
// Incluyendo librerías necesarias
require "./code128.php";
session_start();

// Cargar controlador de configuración de tickets
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

// Configurar PDF
$pdf = new PDF_Code128('P', 'mm', array(80, 258));
$pdf->SetMargins(4, 10, 4);
$pdf->AddPage();

// Obtener configuración de ticket
$tickets = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket(null, null);

// Mostrar configuración del ticket
foreach ($tickets as $ticket) {
    // Encabezado y datos de la empresa
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper($ticket["nombre_empresa"])), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "RUC: ". $ticket["ruc"].""), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Dirección: " . $ticket["direccion"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Teléfono: " . $ticket["telefono"]), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Email: " . $ticket["correo"]), 0, 'C', false);

    // Separador
    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    // Fecha, Cajero y Número de ticket (Este dato lo puedes ajustar según sea necesario)
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Fecha: " . date("d/m/Y")), 0, 'C', false);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", "Emisor: " . $_SESSION["nombre_usuario"]), 0, 'C', false);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", strtoupper("Ticket Nro: " . "12345")), 0, 'C', false);
    $pdf->SetFont('Arial', '', 9);

    // Separador
    $pdf->Ln(1);
    $pdf->Cell(0, 5, iconv("UTF-8", "ISO-8859-1", "------------------------------------------------------"), 0, 0, 'C');
    $pdf->Ln(5);

    // Mostrar mensaje al final del ticket
    $pdf->Ln(10);  // Espacio antes del mensaje
    $pdf->SetFont('Arial', 'I', 8); // Estilo de letra en cursiva y tamaño pequeño
    $pdf->MultiCell(0, 5, iconv("UTF-8", "ISO-8859-1", $ticket["mensaje"]), 0, 'C', false);
    $pdf->Ln(10); // Espacio adicional al final del mensaje

}

// Generando el PDF
if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    // Descargar el PDF
    $pdf->Output('D', 'configuracion_ticket.pdf'); // 'D' fuerza la descarga
} else {
    // Mostrar el PDF en el navegador
    $pdf->Output(); // Muestra el archivo en el navegador
}
