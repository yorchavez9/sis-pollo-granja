<?php



require('../fpdf/fpdf.php');

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);

        // Información de la factura
        $this->SetX(10);
        $this->Cell(0, 7, 'FACTURA', 0, 1, 'L');
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(0, 7, utf8_decode('Factura N° 01234'), 0, 1, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 7, 'Fecha: 02/05/2025', 0, 1, 'L');
        $this->Ln(5);

        // Logotipo
        $this->SetX(170);
        $this->Image('../img/logo.png', 170, 5, 30);
    }

    // Pie de página
    function Footer()
    {
        // Mensaje de agradecimiento
        $this->Ln(80);
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 10, utf8_decode('Gracias por su preferencia'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 9);
        $this->MultiCell(0, 7, utf8_decode('Productos de calidad y un servicio excepcional. Contáctenos para cualquier consulta.'), 0, 'C');
        $this->Ln(5);

        // Información del negocio o empresa
        $this->SetFont('Helvetica', '', 9);
        $this->Cell(0, 7, utf8_decode('Pollerías Isabel S.A.C. | RUC: 20546789123'), 0, 1, 'C');
        $this->Cell(0, 7, utf8_decode('Av. Los Pollos 456, Lima, Perú | Tel: +51 987654321'), 0, 1, 'C');
        $this->Cell(0, 7, 'Email: contacto@polleriasisabel.com', 0, 1, 'C');
        $this->Ln(5);


        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 25);
$pdf->SetFont('Helvetica', '', 10);

// Información del cliente
$pdf->SetX(10);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Cliente:', 0, 1, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(0, 7, 'Isabel Mercado', 0, 1, 'L');
$pdf->Cell(0, 7, 'Documento: 12345678', 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode('Dirección: Av. Siempre Viva 123'), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode('Teléfono: 987654321'), 0, 1, 'L');
$pdf->Cell(0, 7, 'Correo: isabel@example.com', 0, 1, 'L');
$pdf->Ln(5);

// Títulos de la tabla
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX(10);
$pdf->Cell(20, 10, 'Prod.', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Javas', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Aves', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Prom.', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Br.', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Tara', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Merma', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Neto', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'P. Compra', 'T B', 0, 'C', true);
$pdf->Cell(25, 10, 'Total', 'T B', 1, 'C', true);

// Datos de productos
$pdf->SetFont('Helvetica', '', 10);
$productos = [
    ['nombre' => 'Sal', 'javas' => 10, 'aves' => 150, 'peso_prom' => 2.5, 'peso_bruto' => 375, 'peso_tara' => 15, 'merma' => 10, 'peso_neto' => 350, 'p_compra' => 2.50],
    ['nombre' => 'Asucar 2', 'javas' => 5, 'aves' => 70, 'peso_prom' => 2.3, 'peso_bruto' => 161, 'peso_tara' => 7, 'merma' => 5, 'peso_neto' => 154, 'p_compra' => 2.70],
    ['nombre' => 'Pescado 3', 'javas' => 8, 'aves' => 120, 'peso_prom' => 2.8, 'peso_bruto' => 336, 'peso_tara' => 10, 'merma' => 8, 'peso_neto' => 318, 'p_compra' => 2.80],
];

// Ordenar productos por nombre
usort($productos, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

$totalCompra = 0;

foreach ($productos as $index => $producto) {
    $totalProducto = $producto['peso_neto'] * $producto['p_compra'];
    $totalCompra += $totalProducto;
    $borde = ($index === count($productos) - 1) ? 'B' : 0;

    $pdf->SetX(10);
    $pdf->Cell(20, 10, utf8_decode($producto['nombre']), $borde, 0, 'L');
    $pdf->Cell(15, 10, $producto['javas'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['aves'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_prom'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_bruto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_tara'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['merma'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_neto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, 'S/ ' . number_format($producto['p_compra'], 2), $borde, 0, 'C');
    $pdf->Cell(25, 10, 'S/ ' . number_format($totalProducto, 2), $borde, 1, 'C');
}

// Resumen del total
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(150, 10, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($totalCompra, 2), 0, 1, 'R');
$pdf->Cell(150, 10, 'Impuestos (0%):', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ 0.00', 0, 1, 'R');
$pdf->Cell(150, 10, 'Total:', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($totalCompra, 2), 0, 1, 'R');




if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    // Descargar el PDF
    $pdf->Output('D', 'factura.pdf'); // 'D' fuerza la descarga con el nombre 'boleta.pdf'
} else {
    // Mostrar el PDF en el navegador (imprimir)
    $pdf->Output(); // Muestra el archivo en el navegador
}
