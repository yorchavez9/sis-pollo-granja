<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Usuario.controlador.php";
require_once "../../modelos/Usuario.modelo.php";
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

// Iniciar sesión y verificar acceso
session_start();
if (!isset($_SESSION["usuario"])) {
    die("Acceso denegado");
}
$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];

// Obtener configuraciones
$item = null;
$valor = null;
$configuraciones = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($item, $valor);
$usuarios = ControladorUsuarios::ctrMostrarUsuariosReporte($item, $valor);

// Verificar si se obtuvieron usuarios correctamente
if ($usuarios === false || !is_array($usuarios)) {
    die("Error al obtener los datos de usuarios");
}

// Si los usuarios vienen en formato JSON (como parece por tu error)
if (isset($usuarios['data']) && is_array($usuarios['data'])) {
    $usuarios = $usuarios['data'];
}

// Crear una clase extendida de FPDF para personalizar el pie de página
class PDF extends FPDF
{
    function __construct()
    {
        parent::__construct();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

if (count($configuraciones) > 0) {
    $configuracion = $configuraciones[0];

    // Crear el PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 12);

    // Definir márgenes
    $margen_izquierdo = 10;
    $margen_derecho = 10;
    $margen_superior = 10;
    $margen_inferior = 10;

    $pdf->SetLeftMargin($margen_izquierdo);
    $pdf->SetRightMargin($margen_derecho);
    $pdf->SetTopMargin($margen_superior);
    $pdf->SetAutoPageBreak(true, $margen_inferior);

    // Mostrar nombre y logo solo en la primera página
    if ($pdf->PageNo() == 1) {
        // Logo
        if (file_exists("../../uploads/" . $configuracion['logo'])) {
            $pdf->Image("../../uploads/" . $configuracion['logo'], 250, 8, 30);
        }
        // Nombre de la empresa
        $pdf->Cell(0, 10, utf8_decode($configuracion['nombre_empresa']), 0, 1, 'L');
        $pdf->Ln(10);

        // Información adicional
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Generado por: ' . $nombre_usuario, 0, 1, 'L');
        $pdf->Ln(5);
    }

    // Título del reporte
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Reporte de usuarios', 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Calcular factor de escala
    $totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho;
    $totalCellsWidth = 8 + 30 + 48 + 20 + 50 + 25 + 20;
    $scalingFactor = $totalWidth / $totalCellsWidth;

    // Aplicar el factor de escala
    $pdf->Cell(8 * $scalingFactor, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(30 * $scalingFactor, 10, 'Sucursal', 1, 0, 'C', true);
    $pdf->Cell(48 * $scalingFactor, 10, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
    $pdf->Cell(50 * $scalingFactor, 10, 'Correo', 1, 0, 'C', true);
    $pdf->Cell(25 * $scalingFactor, 10, 'Usuario', 1, 0, 'C', true);
    $pdf->Cell(20 * $scalingFactor, 10, 'Estado', 1, 1, 'C', true);

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    foreach ($usuarios as $key => $usuario) {
        $pdf->Cell(8 * $scalingFactor, 10, $key+1, 1, 0, 'C');
        $pdf->Cell(30 * $scalingFactor, 10, utf8_decode($usuario['nombre_sucursal']), 1, 0, 'L');
        $pdf->Cell(48 * $scalingFactor, 10, utf8_decode($usuario['nombre_usuario']), 1, 0, 'L');
        $pdf->Cell(20 * $scalingFactor, 10, $usuario['telefono'], 1, 0, 'C');
        $pdf->Cell(50 * $scalingFactor, 10, utf8_decode($usuario['correo']), 1, 0, 'L');
        $pdf->Cell(25 * $scalingFactor, 10, $usuario['usuario'], 1, 0, 'C');
        $pdf->Cell(20 * $scalingFactor, 10, $usuario['estado'] ? 'Activo' : 'Inactivo', 1, 1, 'C');
    }

    // Limpiar cualquier output previo
    ob_clean();
    
    // Mostrar el PDF
    $pdf->Output('I', 'ReporteUsuarios.pdf');
    exit;
} else {
    die("No se encontraron configuraciones para mostrar.");
}