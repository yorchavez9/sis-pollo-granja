<?php
require('../fpdf/fpdf.php');

require_once "../../controladores/Cliente.controlador.php";
require_once "../../modelos/Cliente.modelo.php";

/* CONFIGURACIONES DE COMPROBANTE */
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

session_start();
if (!isset($_SESSION["usuario"])) {
    die("Acceso denegado");
}

$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];
$razon_social = '';

$item = null;
$valor = null;
$configuraciones = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($item, $valor);

if (
    (isset($_GET["id_cliente"]) && !empty($_GET["id_cliente"])) ||
    (isset($_GET["fecha_desde"]) && !empty($_GET["fecha_desde"])) ||
    (isset($_GET["fecha_hasta"]) && !empty($_GET["fecha_hasta"])) ||
    (isset($_GET["tipo_venta"]) && !empty($_GET["tipo_venta"]))
) {
    $ventas = ControladorCliente::ctrMostrarReporteClientesPDF();
    foreach ($ventas as $value) {
        $razon_social = $value["razon_social"];
    }
} else {
    $ventas = ControladorCliente::ctrMostrarReporteClientes($item, $valor);
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
    $pdf->Cell(0, 10, 'Reporte de ventas por cliente', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 13);
    $pdf->Cell(0, 10, 'Cliente: ' . $razon_social, 0, 1, 'L');
    $pdf->Ln(5);
    // Encabezado de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);

    // Calcular el ancho total disponible para la tabla
    $anchoTotal = $pdf->GetPageWidth() - $margen_izquierdo - $margen_derecho;

    // Definir el ancho de cada columna
    $anchoColumna = [
        'Fecha' => 20,
        'Producto' => 30,
        'Javas' => 15,
        'Aves' => 15,
        'Prom' => 15,
        'Bruto' => 15,
        'Tara' => 15,
        'Neto' => 15,
        'Precio' => 15,
        'Total' => 20,
        'Amortz.' => 20,
        'Saldo' => 20
    ];

    // Ajustar el ancho de las columnas para que ocupen el 100% del ancho disponible
    $factorEscala = $anchoTotal / array_sum($anchoColumna);
    foreach ($anchoColumna as $key => $value) {
        $anchoColumna[$key] = $value * $factorEscala;
    }

    // Encabezado de la tabla con anchos ajustados
    $pdf->Cell($anchoColumna['Fecha'], 10, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Producto'], 10, 'Producto', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Javas'], 10, 'Javas', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Aves'], 10, 'Aves', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Prom'], 10, 'Prom', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Bruto'], 10, 'Bruto', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Tara'], 10, 'Tara', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Neto'], 10, 'Neto', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Precio'], 10, 'Precio', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Total'], 10, 'Total', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Amortz.'], 10, 'Amortz.', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Saldo'], 10, 'Saldo', 1, 1, 'C', true);

    // Contenido de la tabla
    $pdf->SetFont('Arial', '', 10);

    $totalGeneral = 0;
    $totalAmortzGeneral = 0;
    $totalSaldoGeneral = 0;

    $idVentaAnterior = null;
    $subtotalVenta = 0;
    $subtotalAmortzVenta = 0;
    $subtotalSaldoVenta = 0;

    foreach ($ventas as $venta) {
        // Si cambia el id_venta, mostrar subtotal de la venta anterior
        if ($idVentaAnterior !== null && $idVentaAnterior !== $venta['id_venta']) {
            // Mostrar subtotal de la venta anterior
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell($anchoColumna['Fecha'], 10, 'Subtotal Venta', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Producto'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Javas'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Aves'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Prom'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Bruto'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Tara'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Neto'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Precio'], 10, '', 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Total'], 10,'S/ '. $subtotalVenta, 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Amortz.'], 10,'S/ '. $subtotalAmortzVenta, 1, 0, 'C', true);
            $pdf->Cell($anchoColumna['Saldo'], 10,'S/ '. $subtotalSaldoVenta, 1, 1, 'C', true);

            // Reiniciar subtotales para la nueva venta
            $subtotalVenta = 0;
            $subtotalAmortzVenta = 0;
            $subtotalSaldoVenta = 0;
        }

        $idVentaAnterior = $venta['id_venta'];

        // Mostrar detalles de la venta
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($anchoColumna['Fecha'], 10, $venta['fecha_venta'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Producto'], 10, utf8_decode($venta['nombre_producto']), 1, 0, 'L');
        $pdf->Cell($anchoColumna['Javas'], 10, $venta['numero_javas'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Aves'], 10, $venta['numero_aves'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Prom'], 10, $venta['peso_promedio'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Bruto'], 10, $venta['peso_bruto'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Tara'], 10, $venta['peso_tara'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Neto'], 10, $venta['peso_neto'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Precio'], 10, 'S/ '. $venta['precio_venta'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Total'], 10, 'S/ '. $venta['total_venta'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Amortz.'], 10, 'S/ '. $venta['total_pago'], 1, 0, 'C');
        $pdf->Cell($anchoColumna['Saldo'], 10, 'S/ '. $venta['saldo'], 1, 1, 'C');

        // Acumular subtotales de la venta
        $subtotalVenta += $venta['total_venta'];
        $subtotalAmortzVenta += $venta['total_pago'];
        $subtotalSaldoVenta += $venta['saldo'];

        // Acumular totales generales
        $totalGeneral += $venta['total_venta'];
        $totalAmortzGeneral += $venta['total_pago'];
        $totalSaldoGeneral += $venta['saldo'];
    }

    // Mostrar el último subtotal de venta
    if ($idVentaAnterior !== null) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($anchoColumna['Fecha'], 10, 'Subtotal Venta', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Producto'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Javas'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Aves'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Prom'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Bruto'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Tara'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Neto'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Precio'], 10, '', 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Total'], 10, 'S/ '.$subtotalVenta, 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Amortz.'], 10, 'S/ '. $subtotalAmortzVenta, 1, 0, 'C', true);
        $pdf->Cell($anchoColumna['Saldo'], 10, 'S/ '.$subtotalSaldoVenta, 1, 1, 'C', true);
    }

    // Mostrar el total general al final
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($anchoColumna['Fecha'], 10, 'Total General', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Producto'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Javas'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Aves'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Prom'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Bruto'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Tara'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Neto'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Precio'], 10, '', 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Total'], 10,'S/ '. $totalGeneral, 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Amortz.'], 10,'S/ '. $totalAmortzGeneral, 1, 0, 'C', true);
    $pdf->Cell($anchoColumna['Saldo'], 10,'S/ '. $totalSaldoGeneral, 1, 1, 'C', true);

    // Mostrar el PDF
    $pdf->Output();
} else {
    die("No se encontraron configuraciones para mostrar.");
}