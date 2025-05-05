<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Asistencia.controlador.php";
require_once "../../modelos/Asistencia.modelo.php";
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
    (isset($_GET["filtro_trabajador_asistencia"]) && !empty($_GET["filtro_trabajador_asistencia"])) ||
    (isset($_GET["filtro_estado_asistencia"]) && !empty($_GET["filtro_estado_asistencia"])) ||
    (isset($_GET["filtro_fecha_desde_asistencia"]) && !empty($_GET["filtro_fecha_desde_asistencia"])) ||
    (isset($_GET["filtro_fecha_hasta_asistencia"]) && !empty($_GET["filtro_fecha_hasta_asistencia"]))
) {
    $asistencias = ControladorAsistencia::ctrReporteAsistenciaTablePDF();
} else {
    $asistencias = ControladorAsistencia::ctrReporteAsistenciaTable($item, $valor);
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
        $this->SetY(-10);
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
    $pdf->Cell(0, 10, 'Reporte de asistencia de trabajadores', 0, 1, 'C');
    $pdf->Ln(5);

    // Configuración de fuente y color para el encabezado
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Factor de escala ajustado al ancho disponible
    $totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho;
    $totalCellsWidth = 8 + 50 + 35 + 20 + 20 + 20 + 40; // Ajustado al total de columnas
    $scalingFactor = $totalWidth / $totalCellsWidth;

    // Encabezado de la tabla
    $pdf->Cell(8 * $scalingFactor, 10, utf8_decode('N°'), 1, 0, 'C', true);
    $pdf->Cell(50 * $scalingFactor, 10, utf8_decode('Nombre'), 1, 0, 'C', true);
    $pdf->Cell(35 * $scalingFactor, 10, utf8_decode('Fecha'), 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, utf8_decode('Hora Entrada'), 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, utf8_decode('Hora Salida'), 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, utf8_decode('Estado'), 1, 0, 'C', true);
    $pdf->Cell(40 * $scalingFactor, 10, utf8_decode('Observaciones'), 1, 1, 'C', true); // Salto de línea aquí

    // Configuración para el contenido
    $pdf->SetFont('Arial', '', 10);

    // Contenido de la tabla
    foreach ($asistencias as $key => $venta) {
        // Formatear fecha (opcional, asegúrate de que 'fecha_asistencia' esté en formato válido)
        $fecha = DateTime::createFromFormat('Y-m-d', $venta['fecha_asistencia']);
        $fecha_formateada = $fecha ? $fecha->format('d/m/Y') : $venta['fecha_asistencia'];

        // Formatear hora de entrada
        $hora_entrada = DateTime::createFromFormat('H:i:s', $venta['hora_entrada']);
        $hora_entrada_formateada = $hora_entrada ? $hora_entrada->format('h:i A') : $venta['hora_entrada'];

        // Formatear hora de salida
        $hora_salida = DateTime::createFromFormat('H:i:s', $venta['hora_salida']);
        $hora_salida_formateada = $hora_salida ? $hora_salida->format('h:i A') : $venta['hora_salida'];

        // Agregar contenido a la tabla
        $pdf->Cell(8 * $scalingFactor, 10, $key + 1, 1, 0, 'C'); // Número
        $pdf->Cell(50 * $scalingFactor, 10, utf8_decode($venta['nombre']), 1, 0, 'L'); // Nombre
        $pdf->Cell(35 * $scalingFactor, 10, $fecha_formateada, 1, 0, 'C'); // Fecha formateada
        $pdf->Cell(20 * $scalingFactor, 10, $hora_entrada_formateada, 1, 0, 'C'); // Hora entrada formateada
        $pdf->Cell(20 * $scalingFactor, 10, $hora_salida_formateada, 1, 0, 'C'); // Hora salida formateada
        $pdf->Cell(20 * $scalingFactor, 10, utf8_decode($venta['estado']), 1, 0, 'C'); // Estado
        $pdf->Cell(40 * $scalingFactor, 10, utf8_decode($venta['observaciones']), 1, 1, 'L'); // Observaciones con salto
    }



    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
