<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Ventas.controlador.php";
require_once "../../modelos/Ventas.modelo.php";
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
    (isset($_GET["filtro_usuario_venta"]) && !empty($_GET["filtro_usuario_venta"])) ||
    (isset($_GET["filtro_fecha_desde_venta"]) && !empty($_GET["filtro_fecha_desde_venta"])) ||
    (isset($_GET["filtro_fecha_hasta_venta"]) && !empty($_GET["filtro_fecha_hasta_venta"])) ||
    (isset($_GET["filtro_tipo_comprobante_venta"]) && !empty($_GET["filtro_tipo_comprobante_venta"])) ||
    (isset($_GET["filtro_estado_pago_venta"]) && !empty($_GET["filtro_estado_pago_venta"])) ||
    (isset($_GET["filtro_total_venta_min"]) && !empty($_GET["filtro_total_venta_min"])) ||
    (isset($_GET["filtro_total_venta_max"]) && !empty($_GET["filtro_total_venta_max"]))
) {
    $ventas = ControladorVenta::ctrReporteVentasPDF();
} else {
    $ventas = ControladorVenta::ctrMostrarListaVentas($item, $valor);
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
    $pdf->Cell(0, 10, 'Reporte de ventas', 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Factor de escala ajustado al ancho disponible
    $totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho;
    $totalCellsWidth = 8 + 15 + 45 + 35 + 20 + 15 + 20 + 20 + 20;
    $scalingFactor = $totalWidth / $totalCellsWidth;

    // Encabezado
    $pdf->Cell(8 * $scalingFactor, 10, utf8_decode('N°'), 1, 0, 'C', true);
    $pdf->Cell(15 * $scalingFactor, 10, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(25 * $scalingFactor, 10, 'Usuario', 1, 0, 'C', true);
    $pdf->Cell(35 * $scalingFactor, 10, utf8_decode('Cliente'), 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Comprobante', 1, 0, 'C', true);
    $pdf->Cell(15 * $scalingFactor, 10, utf8_decode('Serie N°'), 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Total compra', 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Saldo restante', 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Total pagado', 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Estado pago', 1, 1, 'C', true); // Aquí se hace el salto de línea con `1`

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);

    foreach ($ventas as $key => $venta) {
        $estado = ($venta['estado_pago'] == 'completado') ? 'Completado' : 'Pendiente';
        $saldo_restante = ($venta['total_venta'] - $venta['total_pago']);

        // Ajuste del ancho dinámico según el factor de escala
        $pdf->Cell(8 * $scalingFactor, 10, $key+1, 1, 0, 'C');
        $pdf->Cell(15 * $scalingFactor, 10, $venta['fecha_venta'], 1, 0, 'C');
        $pdf->Cell(25 * $scalingFactor, 10, utf8_decode($venta['nombre_usuario']), 1, 0, 'L');
        $pdf->Cell(35 * $scalingFactor, 10, utf8_decode($venta['razon_social']), 1, 0, 'L');
        $pdf->Cell(20 * $scalingFactor, 10, utf8_decode($venta['tipo_comprobante_sn']), 1, 0, 'L');
        $pdf->Cell(15 * $scalingFactor, 10, utf8_decode($venta['serie_prefijo'] . '-' . $venta["num_comprobante"]), 1, 0, 'C');
        $pdf->Cell(20 * $scalingFactor, 10, 'S/ ' . number_format($venta['total_venta'], 2), 1, 0, 'R');
        $pdf->Cell(20 * $scalingFactor, 10, 'S/ ' . number_format($saldo_restante, 2), 1, 0, 'R');
        $pdf->Cell(20 * $scalingFactor, 10, 'S/ ' . number_format($venta['total_pago'], 2), 1, 0, 'R');
        $pdf->Cell(20 * $scalingFactor, 10, $estado, 1, 1, 'C'); // Salto de línea
    }



    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
