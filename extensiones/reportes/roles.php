<?php
require('../fpdf/fpdf.php');
require_once "../../controladores/Rol.controlador.php";
require_once "../../modelos/Rol.modelo.php";
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
$roles = ControladorRol::ctrMostrarRoles($item, $valor);

// Crear una clase extendida de FPDF para personalizar el pie de página
class PDF extends FPDF
{
    // Constructor
    function __construct()
    {
        parent::__construct(); // Llama al constructor de FPDF
        $this->SetAutoPageBreak(true, 15); // Habilitar salto automático de página con 15mm de margen
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
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Mostrar nombre y logo solo en la primera página
    if ($pdf->PageNo() == 1) {
        // Logo
        if (file_exists("../../uploads/" . $configuracion['logo'])) {
            $pdf->Image("../../uploads/" . $configuracion['logo'], 170, 8, 30); // Logo a la derecha
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
    $pdf->Cell(0, 10, 'Reporte de roles', 0, 1, 'C');
    $pdf->Ln(5);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(110, 10, utf8_decode('Descripción'), 1, 1, 'C', true);

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);
    foreach ($roles as $key => $rol) {
        // Verificamos si hay suficiente espacio en la página para esta fila
        if ($pdf->GetY() > 250) {
            $pdf->AddPage(); // Si no hay suficiente espacio, agregamos una nueva página
        }

        // Dibujamos la celda de ID
        $pdf->Cell(20, 10, $key + 1, 1, 0, 'C');
        // Dibujamos la celda de Nombre
        $pdf->Cell(50, 10, utf8_decode($rol['nombre_rol']), 1, 0, 'L');

        // Guardamos las coordenadas X y Y
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Usamos MultiCell para la descripción
        $pdf->SetXY($x, $y); // Colocamos el cursor en la misma posición para la descripción
        $pdf->MultiCell(110, 10, utf8_decode($rol['descripcion']), 1, 'L');

        // Calculamos la altura ocupada por MultiCell
        $height = $pdf->GetY() - $y;

        // Ajustamos la posición Y para la siguiente fila
        $pdf->SetY($y + $height);
        $pdf->Ln(2); // Añadimos un pequeño espacio extra si es necesario
    }

    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}
