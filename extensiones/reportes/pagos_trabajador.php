<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Pago.trabajador.controlador.php";
require_once "../../modelos/Pago.trabajador.modelo.php";
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
    (isset($_GET["filtro_estado_pago_t"]) && !empty($_GET["filtro_estado_pago_t"])) ||
    (isset($_GET["filtro_fecha_desde_pago_t"]) && !empty($_GET["filtro_fecha_desde_pago_t"])) ||
    (isset($_GET["filtro_fecha_hasta_pago_t"]) && !empty($_GET["filtro_fecha_hasta_pago_t"]))
) {
    $pagos_trabajadores = ControladorPagos::ctrReportePagosTrabajadorPDF();
} else {
    $pagos_trabajadores = ControladorPagos::ctrMostrarPagos($item, $valor);
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
        $this->Cell(0, 10, ('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
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
        $pdf->Cell(0, 10, ($configuracion['nombre_empresa']), 0, 1, 'L'); // Nombre a la izquierda
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

    // Calcular el factor de escala para ajustar el ancho de las celdas
    $totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho; // Ancho total disponible
    $totalCellsWidth = 10 + 35 + 50 + 25 + 30; // Suma de los anchos de las columnas
    $scalingFactor = $totalWidth / $totalCellsWidth; // Factor de escala

    $pdf->Cell(10 * $scalingFactor, 10, ('N°'), 1, 0, 'C', true);
    $pdf->Cell(35 * $scalingFactor, 10, ('Nombre'), 1, 0, 'C', true);
    $pdf->Cell(50 * $scalingFactor, 10, 'Fecha de Pago', 1, 0, 'C', true);
    $pdf->Cell(25 * $scalingFactor, 10, 'Monto', 1, 0, 'C', true);
    $pdf->Cell(30 * $scalingFactor, 10, 'Estado', 1, 1, 'C', true);

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    foreach ($pagos_trabajadores as $key => $pago) {
        // Convertir el estado en texto legible
        $estado = ($pago['estado_pago'] == 1) ? 'Pagado' : 'Pendiente';

        // Formato de las filas
        $pdf->Cell(10 * $scalingFactor, 10, $key + 1, 1, 0, 'C'); // Número
        $pdf->Cell(35 * $scalingFactor, 10, ($pago['nombre']), 1, 0, 'L'); // Nombre
        $pdf->Cell(50 * $scalingFactor, 10, ($pago['fecha_pago']), 1, 0, 'C'); // Fecha de Pago
        $pdf->Cell(25 * $scalingFactor, 10, 'S/ ' . number_format($pago['monto_pago'], 2), 1, 0, 'R'); // Monto formateado
        $pdf->Cell(30 * $scalingFactor, 10, ($estado), 1, 1, 'C'); // Estado
    }



    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
