<?php
require('../fpdf/fpdf.php');
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION["nombre_usuario"])) {
    die("Acceso denegado");
}

$nombre_usuario = $_SESSION["nombre_usuario"];


// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Encabezado
$pdf->Cell(0, 10, 'Reporte de Productos', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Generado por: ' . $nombre_usuario, 0, 1, 'L');
$pdf->Ln(5);

// Encabezado de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Cat.', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Codigo', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Precio', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Stock', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Vencimiento', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Estado', 1, 1, 'C', true);

// Contenido de la tabla
$pdf->SetFont('Arial', '', 10);
foreach ($productos as $producto) {
    $pdf->Cell(20, 10, $producto['id_producto'], 1);
    $pdf->Cell(20, 10, $producto['id_categoria'], 1);
    $pdf->Cell(30, 10, $producto['codigo_producto'], 1);
    $pdf->Cell(40, 10, utf8_decode($producto['nombre_producto']), 1);
    $pdf->Cell(20, 10, number_format($producto['precio_producto'], 2), 1, 0, 'R');
    $pdf->Cell(20, 10, $producto['stock_producto'], 1, 0, 'R');
    $pdf->Cell(30, 10, $producto['fecha_vencimiento'] ?: '-', 1, 0, 'C'); // Mostrar '-' si es NULL
    $pdf->Cell(20, 10, $producto['estado_producto'] ? 'Activo' : 'Inactivo', 1, 1, 'C');
}

// Cerrar conexión
$conexion->close();

// Mostrar el PDF
$pdf->Output();
