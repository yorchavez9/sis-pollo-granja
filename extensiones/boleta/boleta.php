<?php
require('../fpdf/fpdf.php');

session_start();

$nombre_usuario = $_SESSION["nombre_usuario"];

require_once "../../controladores/Compra.controlador.php";
require_once "../../controladores/Producto.controlador.php";
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Compra.modelo.php";
require_once "../../modelos/Producto.modelo.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

function formatearPrecio($precio)
{
    return number_format($precio, 2, '.', ',');
}

/* ========================================
MOSTRANDO DATOS DE LA VENTA
======================================== */
$item = "id_egreso";
$valor = $_GET["id_egreso"];

$respuesta = ControladorCompra::ctrMostrarCompras($item, $valor);

$respuesta_de = ControladorCompra::ctrMostrarDetalleCompra($item, $valor);
$horaVenta = $respuesta["fecha_egre"];
$impuesto = $respuesta["impuesto"];  // Obtenemos el valor del impuesto
$horaFormateada = date("h:i A", strtotime($horaVenta));

$itemConfig = null;
$valorConfig = null;

$tickets = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($itemConfig, $valorConfig);

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);

        // Información de la factura
        $this->SetX(10);
        $this->Cell(0, 7, 'BOLETA', 0, 1, 'L');
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(0, 7, utf8_decode('Boleta N° 01234'), 0, 1, 'L');
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

$subtotal = 0;
usort($respuesta_de, fn($a, $b) => strcmp($a['nombre_producto'], $b['nombre_producto']));

$totalCompra = 0;

foreach ($respuesta_de as $index => $producto) {
    $totalProducto = $producto['peso_neto'] * $producto['precio_compra'];
    $totalCompra += $totalProducto;
    $borde = ($index === count($respuesta_de) - 1) ? 'B' : 0;
    $pdf->SetX(10);
    $pdf->Cell(20, 10, utf8_decode($producto['nombre_producto']), $borde, 0, 'L');
    $pdf->Cell(15, 10, $producto['numero_javas'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['numero_aves'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_promedio'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_bruto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_tara'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['peso_merma'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_neto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, 'S/ ' . number_format($producto['precio_compra'], 2), $borde, 0, 'C');
    $pdf->Cell(25, 10, 'S/ ' . number_format($totalProducto, 2), $borde, 1, 'C');
}

// Cálculo del impuesto y el total
$impuestoTotal = $totalCompra * ($impuesto / 100);
$totalConImpuesto = $totalCompra + $impuestoTotal;

// Resumen del total
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(150, 10, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($totalCompra, 2), 0, 1, 'R');
$pdf->Cell(150, 10, 'Impuestos (' . $impuesto . '%):', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($impuestoTotal, 2), 0, 1, 'R');
// Total con borde superior e inferior
$pdf->Cell(150, 10, 'Total:', 'TB', 0, 'R'); // Borde superior e inferior en la celda de "Total"
$pdf->Cell(40, 10, 'S/ ' . number_format($totalConImpuesto, 2), 'TB', 1, 'R'); // Borde superior e inferior en la celda del total

if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    // Descargar el PDF
    $pdf->Output('D', 'boleta.pdf'); // 'D' fuerza la descarga con el nombre 'boleta.pdf'
} else {
    // Mostrar el PDF en el navegador (imprimir)
    $pdf->Output(); // Muestra el archivo en el navegador
}
