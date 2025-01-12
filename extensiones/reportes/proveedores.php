<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Proveedor.controlador.php"; // Cambiar al controlador de proveedores
require_once "../../modelos/Proveedor.modelo.php"; // Cambiar al modelo de proveedores
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

session_start();
if (!isset($_SESSION["nombre_usuario"])) {
    die("Acceso denegado");
}
$nombre_usuario = $_SESSION["nombre_usuario"];

$item = null;
$valor = null;
$configuraciones = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($item, $valor);
$proveedores = ControladorProveedores::ctrMostrarProveedor($item, $valor); // Cambiar a la función que trae proveedores

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
    $pdf->Cell(0, 10, 'Reporte de Proveedores', 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Calcular factor de escala
    $totalWidth = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho; // Ancho total disponible
    $totalCellsWidth = 8 + 40 + 100 + 20 + 50 + 50 + 30 + 20; // Ancho total de las celdas de la tabla (ajustar a los campos correctos)
    $scalingFactor = $totalWidth / $totalCellsWidth; // Factor de escala

    // Aplicar el factor de escala a cada celda del encabezado
    $pdf->Cell(8 * $scalingFactor, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(100 * $scalingFactor, 10, utf8_decode('Razón Social'), 1, 0, 'C', true);
    $pdf->Cell(30 * $scalingFactor, 10, 'Direccion', 1, 0, 'C', true);
    $pdf->Cell(40 * $scalingFactor, 10, 'Documento', 1, 0, 'C', true);
    $pdf->Cell(40 * $scalingFactor, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
    $pdf->Cell(70 * $scalingFactor, 10, 'Correo', 1, 0, 'C', true);
    $pdf->Cell(30 * $scalingFactor, 10, 'Estado', 1, 1, 'C', true);

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    foreach ($proveedores as $key => $proveedor) {
        $pdf->Cell(8 * $scalingFactor, 10, $key + 1, 1, 0, 'C');
        $pdf->Cell(100 * $scalingFactor, 10, utf8_decode($proveedor['razon_social']), 1, 0, 'L');
        $pdf->Cell(30 * $scalingFactor, 10, $proveedor['direccion'], 1, 0, 'C');
        $pdf->Cell(40 * $scalingFactor, 10, $proveedor['numero_documento'], 1, 0, 'C');
        $pdf->Cell(40 * $scalingFactor, 10, $proveedor['telefono'], 1, 0, 'C');
        $pdf->Cell(70 * $scalingFactor, 10, utf8_decode($proveedor['email']), 1, 0, 'L');
        $pdf->Cell(30 * $scalingFactor, 10, $proveedor['estado_persona'] ? 'Activo' : 'Inactivo', 1, 1, 'C');
    }

    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
