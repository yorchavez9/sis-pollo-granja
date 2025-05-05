<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Gastos.ingreso.controlador.php";
require_once "../../modelos/Gastos.ingreso.modelo.php";
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

session_start();
if (!isset($_SESSION["usuario"])) {
    die("Acceso denegado");
}

$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];

$item = null;
$valor = null;
$configuraciones = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($item, $valor);
if (
    (isset($_GET["id_usuario_reporte"]) && !empty($_GET["id_usuario_reporte"])) ||
    (isset($_GET["tipo_reporte"]) && ($_GET["tipo_reporte"] !== '')) ||
    (isset($_GET["fecha_desde_reporte"]) && !empty($_GET["fecha_desde_reporte"])) ||
    (isset($_GET["fecha_hasta_reporte"]) && !empty($_GET["fecha_hasta_reporte"]))
) {
    $respuesta = ControladorGastoIngreso::ctrReporteGastosIngresosPDF();
} else {
    $respuesta = ControladorGastoIngreso::ctrMostrarGastoIngreso($item, $valor);
}


// Crear una clase extendida de FPDF para personalizar el pie de página
class PDF extends FPDF
{
    // Constructor
    function __construct()
    {
        parent::__construct(); // Llama al constructor de FPDF
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

if (count($configuraciones) > 0) {
    $configuracion = $configuraciones[0]; // Tomar la primera configuración

    // Crear el PDF con la clase extendida
    $pdf = new PDF();
    $pdf->AliasNbPages(); // Activa el uso del número total de páginas
    $pdf->AddPage('L'); // Añadir página con orientación horizontal (Landscape)
    $pdf->SetFont('Arial', 'B', 12);

    // Definir márgenes mínimos
    $margen_izquierdo = 10;
    $margen_derecho = 10;
    $margen_superior = 10;
    $margen_inferior = 10;

    // Establecer márgenes de la página
    $pdf->SetLeftMargin($margen_izquierdo);
    $pdf->SetRightMargin($margen_derecho);
    $pdf->SetTopMargin($margen_superior);
    $pdf->SetAutoPageBreak(true, $margen_inferior);

    // Mostrar nombre y logo solo en la primera página
    if ($pdf->PageNo() == 1) {
        // Logo
        if (file_exists("../../uploads/" . $configuracion['logo'])) {
            $pdf->Image("../../uploads/" . $configuracion['logo'], 250, 8, 30); // Logo a la derecha
        }
        // Nombre de la empresa
        $pdf->Cell(0, 10, utf8_decode($configuracion['nombre_empresa']), 0, 1, 'L'); // Nombre a la izquierda
        $pdf->Ln(10);

        // Información adicional
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Generado por: ' . $nombre_usuario, 0, 1, 'L');
        $pdf->Ln(5);
    }

    // Título del reporte
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Reporte de productos', 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Calcular el ancho total disponible
$totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho;

// Ancho relativo de cada celda (proporciones)
$relativeWidths = [
    5, // N°
    20, // Fecha
    10, // Tipo
    40, // Concepto
    20, // Monto
    30  // Detalles
];

// Calcular la suma total de las proporciones
$totalRelativeWidth = array_sum($relativeWidths);

// Factor de escala para ajustar las celdas al 100% del ancho disponible
$scalingFactor = $totalWidth / $totalRelativeWidth;

// Calcular los anchos escalados para cada celda
$scaledWidths = array_map(fn($width) => $width * $scalingFactor, $relativeWidths);

// Encabezado de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($scaledWidths[0], 10, utf8_decode('N°'), 1, 0, 'C', true);
$pdf->Cell($scaledWidths[1], 10, utf8_decode('Fecha'), 1, 0, 'C', true);
$pdf->Cell($scaledWidths[2], 10, 'Tipo', 1, 0, 'C', true);
$pdf->Cell($scaledWidths[3], 10, 'Concepto', 1, 0, 'C', true);
$pdf->Cell($scaledWidths[4], 10, 'Monto', 1, 0, 'C', true);
$pdf->Cell($scaledWidths[5], 10, 'Detalles', 1, 1, 'C', true);

// Contenido de la tabla
$pdf->SetFont('Arial', '', 10);
foreach ($respuesta as $key => $data) {
    $pdf->Cell($scaledWidths[0], 10, $key + 1, 1, 0, 'C');
    $pdf->Cell($scaledWidths[1], 10, utf8_decode($data['fecha']), 1, 0, 'L');
    $pdf->Cell($scaledWidths[2], 10, utf8_decode($data['tipo']), 1, 0, 'L');
    $pdf->Cell($scaledWidths[3], 10, $data['concepto'], 1, 0, 'C');
    $pdf->Cell($scaledWidths[4], 10, 'S/ ' . number_format($data['monto'], 2), 1, 0, 'C');
    $pdf->Cell($scaledWidths[5], 10, utf8_decode($data['detalles']), 1, 1, 'C'); // Última celda pasa a la siguiente línea
}


    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
