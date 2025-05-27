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

// Obtener configuración de la empresa
$item = null;
$valor = null;
$configuraciones = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($item, $valor);

// Obtener datos filtrados
$filtros = [
    "id_cliente" => $_GET["id_cliente"] ?? null,
    "fecha_desde" => $_GET["fecha_desde"] ?? null,
    "fecha_hasta" => $_GET["fecha_hasta"] ?? null,
    "tipo_venta" => $_GET["tipo_venta"] ?? null,
    "estado_pago" => $_GET["estado_pago"] ?? null,
    "tasa_cambio" => $_GET["tasa_cambio"] ?? null
];

$ventas = ControladorCliente::ctrMostrarReporteClientesPDF($filtros);

// Obtener nombre del cliente si hay filtro
if (!empty($filtros["id_cliente"])) {
    $cliente = ControladorCliente::ctrMostrarCliente("id_persona", $filtros["id_cliente"]);
    $razon_social = $cliente["razon_social"] ?? '';
}

class PDF extends FPDF
{
    private $config;
    private $nombreUsuario;
    private $razonSocial;
    private $tasaCambio;
    private $filtros;
    private $colorPrimario = [41, 128, 185]; // Azul moderno
    private $colorSecundario = [52, 73, 94]; // Gris azulado
    private $colorAccento = [231, 76, 60]; // Rojo para saldos
    private $colorExito = [39, 174, 96]; // Verde para pagado

    function __construct($config, $nombreUsuario, $razonSocial, $tasaCambio, $filtros)
    {
        parent::__construct('L', 'mm', 'A4');
        $this->config = $config;
        $this->nombreUsuario = $nombreUsuario;
        $this->razonSocial = $razonSocial;
        $this->tasaCambio = $tasaCambio;
        $this->filtros = $filtros;
        
        $this->SetMargins(8, 8, 8);
        $this->SetAutoPageBreak(true, 12);
    }

    // Función para crear degradado horizontal
    function LinearGradient($x, $y, $w, $h, $col1, $col2)
    {
        $this->SetFillColor($col1[0], $col1[1], $col1[2]);
        $this->Rect($x, $y, $w/2, $h, 'F');
        $this->SetFillColor($col2[0], $col2[1], $col2[2]);
        $this->Rect($x + $w/2, $y, $w/2, $h, 'F');
    }

    // Función para crear rectángulos redondeados (simulado)
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $this->Rect($x, $y, $w, $h, $style);
    }

    function Header()
    {
        // Fondo moderno para el header
        $this->LinearGradient(0, 0, 297, 45, $this->colorPrimario, [52, 152, 219]);
        
        // Logo con marco elegante
        if (file_exists("../../uploads/" . $this->config['logo'])) {
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect(245, 6, 40, 32, 2, 'F');
            $this->Image("../../uploads/" . $this->config['logo'], 248, 8, 34);
        }
        
        // Información de empresa con estilo moderno
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 18);
        $this->SetXY(15, 10);
        $this->Cell(0, 8, utf8_decode($this->config['nombre_empresa']), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 9);
        $this->SetXY(15, 20);
        $info_empresa = 'RUC: ' . $this->config['ruc'] . '  -  ' . 
                       utf8_decode($this->config['direccion']) . '  -  ' .
                       'Tel: ' . $this->config['telefono'];
        $this->Cell(0, 5, $info_empresa, 0, 1, 'L');
        
        // Título principal con diseño moderno
        $this->SetY(50);
        $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, 'REPORTE DE VENTAS POR CLIENTE', 0, 1, 'C');
        
        // Línea decorativa
        $this->SetDrawColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->SetLineWidth(0.8);
        $this->Line(50, 62, 247, 62);
        
        // Información del cliente con estilo
        $this->SetY(68);
        if (!empty($this->razonSocial)) {
            $this->SetFillColor(248, 249, 250);
            $this->RoundedRect(15, 68, 267, 8, 1, 'F');
            $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
            $this->SetFont('Arial', 'B', 11);
            $this->SetXY(20, 70);
            $this->Cell(0, 6, 'CLIENTE: ' . utf8_decode($this->razonSocial), 0, 1, 'L');
            $this->SetY(78);
        }
        
        // Panel de filtros con diseño moderno
        $filtrosAplicados = [];
        if (!empty($this->filtros['fecha_desde']) && !empty($this->filtros['fecha_hasta'])) {
            $filtrosAplicados[] = 'Periodo: ' . $this->filtros['fecha_desde'] . ' - ' . $this->filtros['fecha_hasta'];
        }
        if (!empty($this->filtros['tipo_venta'])) {
            $filtrosAplicados[] = 'Tipo: ' . ($this->filtros['tipo_venta'] == 'credito' ? 'Crédito' : 'Contado');
        }
        if (!empty($this->filtros['estado_pago'])) {
            $filtrosAplicados[] = 'Estado: ' . ($this->filtros['estado_pago'] == 'completado' ? 'Pagado' : 'Pendiente');
        }
        
        if (!empty($filtrosAplicados)) {
            $y_current = $this->GetY();
            $this->SetFillColor(236, 240, 241);
            $this->RoundedRect(15, $y_current, 200, 6, 1, 'F');
            $this->SetTextColor(127, 140, 141);
            $this->SetFont('Arial', '', 8);
            $this->SetXY(20, $y_current + 1);
            $this->Cell(0, 4, 'FILTROS: ' . implode(' • ', $filtrosAplicados), 0, 0, 'L');
            $this->SetY($y_current + 8);
        }
        
        // Información de generación
        $y_current = $this->GetY();
        $this->SetTextColor(149, 165, 166);
        $this->SetFont('Arial', 'I', 8);
        $this->SetXY(220, $y_current);
        $this->Cell(0, 4, 'Generado por: ' . $this->nombreUsuario, 0, 1, 'R');
        $this->SetXY(220, $y_current + 4);
        $this->Cell(0, 4, date('d/m/Y H:i'), 0, 1, 'R');
        
        $this->Ln(8);
    }

    function Footer()
    {
        // Footer moderno con degradado
        $this->SetY(-15);
        $this->LinearGradient(0, -15, 297, 15, [236, 240, 241], [189, 195, 199]);
        
        $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 15, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

if (count($configuraciones) > 0) {
    $configuracion = $configuraciones[0];
    
    $pdf = new PDF($configuracion, $nombre_usuario, $razon_social, $filtros['tasa_cambio'], $filtros);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Anchos optimizados para diseño moderno
    $anchos = [
        'fecha' => 22,
        'producto' => 48,
        'javas' => 16,
        'aves' => 16,
        'prom' => 16,
        'bruto' => 16,
        'tara' => 16,
        'neto' => 16,
        'precio' => 22,
        'total' => 24,
        'pago' => 24,
        'saldo' => 24
    ];
    
    // Header de tabla con diseño moderno
    $pdf->SetFont('Arial', 'B', 9);
    
    // Degradado para header
    $y_pos = $pdf->GetY();
    $pdf->LinearGradient(15, $y_pos, 267, 8, [52, 152, 219], [41, 128, 185]);
    
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY(15, $y_pos);
    
    $headers = [
        'Fecha', 'Producto', 'Javas', 'Aves', 'P.Prom', 
        'P.Bruto', 'P.Tara', 'P.Neto', 'Precio Unit.', 
        'Total', 'Pagado', 'Saldo'
    ];
    
    foreach ($headers as $i => $header) {
        $width = array_values($anchos)[$i];
        $pdf->Cell($width, 8, $header, 1, 0, 'C');
    }
    $pdf->Ln();
    
    // Variables para totales
    $totalGeneral = 0;
    $totalPagado = 0;
    $totalSaldo = 0;
    $currentVentaId = null;
    $subtotalVenta = 0;
    $subtotalPagado = 0;
    $subtotalSaldo = 0;
    $rowColor = true;
    
    $pdf->SetFont('Arial', '', 8);
    
    foreach ($ventas as $index => $venta) {
        // Verificar si cambió de venta para subtotal
        if ($currentVentaId !== null && $currentVentaId != $venta['id_venta']) {
            // Subtotal con estilo moderno
            $y_pos = $pdf->GetY();
            $pdf->SetFillColor(250, 250, 250);
            $pdf->Rect(15, $y_pos, 267, 6, 'F');
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(93, 109, 126);
            $pdf->SetXY(15, $y_pos);
            
            $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
                      $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
                      $anchos['precio'], 6, 'SUBTOTAL', 0, 0, 'R');
            $pdf->Cell($anchos['total'], 6, 'S/ ' . number_format($subtotalVenta, 2), 0, 0, 'R');
            $pdf->Cell($anchos['pago'], 6, 'S/ ' . number_format($subtotalPagado, 2), 0, 0, 'R');
            $pdf->Cell($anchos['saldo'], 6, 'S/ ' . number_format($subtotalSaldo, 2), 0, 1, 'R');
            
            // Reiniciar subtotales
            $subtotalVenta = $subtotalPagado = $subtotalSaldo = 0;
            $pdf->SetFont('Arial', '', 8);
        }
        
        $currentVentaId = $venta['id_venta'];
        
        // Acumular totales
        $subtotalVenta += $venta['total_venta'];
        $subtotalPagado += $venta['total_pago'];
        $subtotalSaldo += $venta['saldo'];
        $totalGeneral += $venta['total_venta'];
        $totalPagado += $venta['total_pago'];
        $totalSaldo += $venta['saldo'];
        
        // Alternar colores de fila para mejor legibilidad
        $y_pos = $pdf->GetY();
        if ($rowColor) {
            $pdf->SetFillColor(249, 250, 251);
            $pdf->Rect(15, $y_pos, 267, 5, 'F');
        }
        $rowColor = !$rowColor;
        
        $pdf->SetTextColor(52, 73, 94);
        $pdf->SetXY(15, $y_pos);
        
        // Datos de la fila
        $pdf->Cell($anchos['fecha'], 5, date('d/m/Y', strtotime($venta['fecha_venta'])), 0, 0, 'C');
        $pdf->Cell($anchos['producto'], 5, utf8_decode(substr($venta['nombre_producto'], 0, 28)), 0, 0, 'L');
        $pdf->Cell($anchos['javas'], 5, $venta['numero_javas'], 0, 0, 'C');
        $pdf->Cell($anchos['aves'], 5, $venta['numero_aves'], 0, 0, 'C');
        $pdf->Cell($anchos['prom'], 5, $venta['peso_promedio'], 0, 0, 'C');
        $pdf->Cell($anchos['bruto'], 5, $venta['peso_bruto'], 0, 0, 'C');
        $pdf->Cell($anchos['tara'], 5, $venta['peso_tara'], 0, 0, 'C');
        $pdf->Cell($anchos['neto'], 5, $venta['peso_neto'], 0, 0, 'C');
        $pdf->Cell($anchos['precio'], 5, 'S/ ' . number_format($venta['precio_venta'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['total'], 5, 'S/ ' . number_format($venta['total_venta'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['pago'], 5, 'S/ ' . number_format($venta['total_pago'], 2), 0, 0, 'R');
        
        // Saldo con color según estado
        if ($venta['saldo'] > 0) {
            $pdf->SetTextColor(231, 76, 60); // Rojo para pendientes
        } else {
            $pdf->SetTextColor(39, 174, 96); // Verde para pagado
        }
        $pdf->Cell($anchos['saldo'], 5, 'S/ ' . number_format($venta['saldo'], 2), 0, 1, 'R');
        $pdf->SetTextColor(52, 73, 94);
    }
    
    // Último subtotal
    if (!empty($ventas)) {
        $y_pos = $pdf->GetY();
        $pdf->SetFillColor(250, 250, 250);
        $pdf->Rect(15, $y_pos, 267, 6, 'F');
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(93, 109, 126);
        $pdf->SetXY(15, $y_pos);
        
        $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
                  $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
                  $anchos['precio'], 6, 'SUBTOTAL', 0, 0, 'R');
        $pdf->Cell($anchos['total'], 6, 'S/ ' . number_format($subtotalVenta, 2), 0, 0, 'R');
        $pdf->Cell($anchos['pago'], 6, 'S/ ' . number_format($subtotalPagado, 2), 0, 0, 'R');
        $pdf->Cell($anchos['saldo'], 6, 'S/ ' . number_format($subtotalSaldo, 2), 0, 1, 'R');
    }
    
    // Total general con diseño premium
    $pdf->Ln(3);
    $y_pos = $pdf->GetY();
    $pdf->LinearGradient(15, $y_pos, 267, 10, [41, 128, 185], [52, 152, 219]);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY(15, $y_pos + 1);
    
    $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
              $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
              $anchos['precio'], 8, 'TOTAL GENERAL', 0, 0, 'R');
    $pdf->Cell($anchos['total'], 8, 'S/ ' . number_format($totalGeneral, 2), 0, 0, 'R');
    $pdf->Cell($anchos['pago'], 8, 'S/ ' . number_format($totalPagado, 2), 0, 0, 'R');
    $pdf->Cell($anchos['saldo'], 8, 'S/ ' . number_format($totalSaldo, 2), 0, 1, 'R');
    
    // Panel de conversión a dólares (si aplica)
    if (!empty($filtros['tasa_cambio']) && is_numeric($filtros['tasa_cambio']) && $filtros['tasa_cambio'] > 0) {
        $pdf->Ln(5);
        $y_pos = $pdf->GetY();
        
        $pdf->SetFillColor(236, 240, 241);
        $pdf->RoundedRect(15, $y_pos, 267, 20, 2, 'F');
        
        $pdf->SetTextColor(52, 73, 94);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(20, $y_pos + 3);
        $pdf->Cell(0, 6, 'EQUIVALENTE EN '.utf8_decode('DÓLARES USD').' (Tasa: ' . number_format($filtros['tasa_cambio'], 4) . ')', 0, 1);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(25, $y_pos + 10);
        $pdf->Cell(80, 5, 'Total Ventas: USD ' . number_format($totalGeneral * $filtros['tasa_cambio'], 2), 0, 0);
        $pdf->Cell(80, 5, 'Total Pagado: USD ' . number_format($totalPagado * $filtros['tasa_cambio'], 2), 0, 0);
        $pdf->Cell(0, 5, 'Saldo Pendiente: USD ' . number_format($totalSaldo * $filtros['tasa_cambio'], 2), 0, 1);
    }
    
    // Nota final con estilo
    $pdf->Ln(8);
    $y_pos = $pdf->GetY();
    $pdf->SetFillColor(255, 248, 220);
    $pdf->RoundedRect(15, $y_pos, 267, 8, 1, 'F');
    
    $pdf->SetTextColor(138, 109, 59);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetXY(20, $y_pos + 2);
    $pdf->Cell(0, 4, utf8_decode('📊 Este documento es un reporte informativo generado el ') . date('d/m/Y H:i') . utf8_decode(' • Los montos están expresados en Soles (S/)'), 0, 1);
    
    $pdf->Output('Reporte_Ventas_Cliente_' . date('Ymd_His') . '.pdf', 'I');
} else {
    die("No se encontraron configuraciones para mostrar.");
}
?>