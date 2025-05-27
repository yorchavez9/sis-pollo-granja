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
    public $colorPrimario = [41, 128, 185];
    public $colorSecundario = [52, 73, 94];
    public $colorAccento = [231, 76, 60];
    public $colorExito = [39, 174, 96];
    public $colorFondo = [250, 250, 252];
    public $colorBorde = [225, 228, 232];

    function __construct($config, $nombreUsuario, $razonSocial, $tasaCambio, $filtros)
    {
        parent::__construct('L', 'mm', 'A4');
        $this->config = $config;
        $this->nombreUsuario = $nombreUsuario;
        $this->razonSocial = $razonSocial;
        $this->tasaCambio = $tasaCambio;
        $this->filtros = $filtros;
        
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 15);
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
        // Fondo moderno para el header con sombra
        $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);
        $this->Rect(0, 0, 297, 50, 'F');
        
        // Barra superior con degradado
        $this->LinearGradient(0, 0, 297, 12, $this->colorPrimario, [52, 152, 219]);
        
        // Logo con marco circular
        if (file_exists("../../uploads/" . $this->config['logo'])) {
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect(245, 8, 40, 40, 20, 'F');
            $this->Image("../../uploads/" . $this->config['logo'], 248, 11, 34, 34);
        }
        
        // Información de empresa con estilo moderno
        $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
        $this->SetFont('Arial', 'B', 16);
        $this->SetXY(15, 15);
        $this->Cell(0, 8, utf8_decode($this->config['nombre_empresa']), 0, 1, 'L');
        
        $this->SetFont('Helvetica', '', 9);
        $this->SetXY(15, 23);
        $info_empresa = 'RUC: ' . $this->config['ruc'] . '  -  ' . 
                       utf8_decode($this->config['direccion']) . '  -  ' .
                       'Tel: ' . $this->config['telefono'];
        $this->Cell(0, 5, $info_empresa, 0, 1, 'L');
        
        // Título principal con diseño moderno
        $this->SetY(45);
        $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'REPORTE DE VENTAS POR CLIENTE', 0, 1, 'C');
        
        // Línea decorativa con puntos
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        $this->Line(50, 57, 247, 57);
        $this->SetDrawColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
        $this->SetLineWidth(0.5);
        $this->Line(50, 58, 247, 58);
        
        // Información del cliente con estilo de tarjeta
        $this->SetY(65);
        if (!empty($this->razonSocial)) {
            $this->SetFillColor(245, 247, 250);
            $this->RoundedRect(15, 65, 267, 10, 2, 'F');
            $this->SetDrawColor($this->colorBorde[0], $this->colorBorde[1], $this->colorBorde[2]);
            $this->RoundedRect(15, 65, 267, 10, 2, 'D');
            
            $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
            $this->SetFont('Arial', 'B', 11);
            $this->SetXY(20, 67);
            $this->Cell(0, 6, 'CLIENTE: ' . utf8_decode($this->razonSocial), 0, 1, 'L');
            $this->SetY(77);
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
            $this->SetFillColor(240, 242, 245);
            $this->RoundedRect(15, $y_current, 200, 6, 1, 'F');
            $this->SetDrawColor($this->colorBorde[0], $this->colorBorde[1], $this->colorBorde[2]);
            $this->RoundedRect(15, $y_current, 200, 6, 1, 'D');
            
            $this->SetTextColor(120, 134, 156);
            $this->SetFont('Arial', '', 8);
            $this->SetXY(20, $y_current + 1);
            $this->Cell(0, 4, 'FILTROS: ' . implode(' • ', $filtrosAplicados), 0, 0, 'L');
            $this->SetY($y_current + 8);
        }
        
        // Información de generación con estilo minimalista
        $y_current = $this->GetY();
        $this->SetTextColor(149, 165, 166);
        $this->SetFont('Arial', 'I', 8);
        $this->SetXY(220, $y_current);
        $this->Cell(0, 4, 'Generado por: ' . $this->nombreUsuario, 0, 1, 'R');
        $this->SetXY(220, $y_current + 4);
        $this->Cell(0, 4, date('d/m/Y H:i'), 0, 1, 'R');
        
        $this->Ln(10);
    }

    function Footer()
    {
        // Footer moderno con borde superior
        $this->SetY(-15);
        $this->SetFillColor(245, 247, 250);
        $this->Rect(0, -15, 297, 15, 'F');
        $this->SetDrawColor($this->colorBorde[0], $this->colorBorde[1], $this->colorBorde[2]);
        $this->Line(10, -15, 287, -15);
        
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
    $pdf->SetFont('Helvetica', 'B', 9);
    
    // Fondo para header de tabla
    $y_pos = $pdf->GetY();
    $pdf->SetFillColor(240, 242, 245);
    $pdf->Rect(10, $y_pos, 277, 8, 'F');
    $pdf->SetDrawColor($pdf->colorBorde[0], $pdf->colorBorde[1], $pdf->colorBorde[2]);
    $pdf->Rect(10, $y_pos, 277, 8, 'D');
    
    $pdf->SetTextColor($pdf->colorSecundario[0], $pdf->colorSecundario[1], $pdf->colorSecundario[2]);
    $pdf->SetXY(10, $y_pos);
    
    $headers = [
        'Fecha', 'Producto', 'Javas', 'Aves', 'P.Prom', 
        'P.Bruto', 'P.Tara', 'P.Neto', 'Precio Unit.', 
        'Total', 'Pagado', 'Saldo'
    ];
    
    foreach ($headers as $i => $header) {
        $width = array_values($anchos)[$i];
        $pdf->Cell($width, 8, $header, 0, 0, 'C');
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
    
    $pdf->SetFont('Helvetica', '', 8);
    
    foreach ($ventas as $index => $venta) {
        // Verificar si cambió de venta para subtotal
        if ($currentVentaId !== null && $currentVentaId != $venta['id_venta']) {
            // Subtotal con estilo moderno
            $y_pos = $pdf->GetY();
            $pdf->SetFillColor(245, 247, 250);
            $pdf->Rect(10, $y_pos, 277, 6, 'F');
            $pdf->SetDrawColor($pdf->colorBorde[0], $pdf->colorBorde[1], $pdf->colorBorde[2]);
            $pdf->Rect(10, $y_pos, 277, 6, 'D');
            
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->SetTextColor(93, 109, 126);
            $pdf->SetXY(10, $y_pos);
            
            $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
                      $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
                      $anchos['precio'], 6, 'SUBTOTAL', 0, 0, 'R');
            $pdf->Cell($anchos['total'], 6, 'S/ ' . number_format($subtotalVenta, 2), 0, 0, 'R');
            $pdf->Cell($anchos['pago'], 6, 'S/ ' . number_format($subtotalPagado, 2), 0, 0, 'R');
            $pdf->Cell($anchos['saldo'], 6, 'S/ ' . number_format($subtotalSaldo, 2), 0, 1, 'R');
            
            // Reiniciar subtotales
            $subtotalVenta = $subtotalPagado = $subtotalSaldo = 0;
            $pdf->SetFont('Helvetica', '', 8);
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
            $pdf->SetFillColor(250, 252, 255);
            $pdf->Rect(10, $y_pos, 277, 5, 'F');
        }
        $rowColor = !$rowColor;
        
        $pdf->SetTextColor(52, 73, 94);
        $pdf->SetXY(10, $y_pos);
        
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
        $pdf->SetFillColor(245, 247, 250);
        $pdf->Rect(10, $y_pos, 277, 6, 'F');
        $pdf->SetDrawColor($pdf->colorBorde[0], $pdf->colorBorde[1], $pdf->colorBorde[2]);
        $pdf->Rect(10, $y_pos, 277, 6, 'D');
        
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(93, 109, 126);
        $pdf->SetXY(10, $y_pos);
        
        $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
                  $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
                  $anchos['precio'], 6, 'SUBTOTAL', 0, 0, 'R');
        $pdf->Cell($anchos['total'], 6, 'S/ ' . number_format($subtotalVenta, 2), 0, 0, 'R');
        $pdf->Cell($anchos['pago'], 6, 'S/ ' . number_format($subtotalPagado, 2), 0, 0, 'R');
        $pdf->Cell($anchos['saldo'], 6, 'S/ ' . number_format($subtotalSaldo, 2), 0, 1, 'R');
    }
    
    // Total general con diseño premium
    $pdf->Ln(5);
    $y_pos = $pdf->GetY();
    
    // Fondo con degradado suave
    $pdf->LinearGradient(10, $y_pos, 277, 10, [240, 242, 245], [230, 233, 237]);
    $pdf->SetDrawColor($pdf->colorPrimario[0], $pdf->colorPrimario[1], $pdf->colorPrimario[2]);
    $pdf->Rect(10, $y_pos, 277, 10, 'D');
    
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor($pdf->colorPrimario[0], $pdf->colorPrimario[1], $pdf->colorPrimario[2]);
    $pdf->SetXY(10, $y_pos + 1);
    
    $pdf->Cell($anchos['fecha'] + $anchos['producto'] + $anchos['javas'] + $anchos['aves'] + 
              $anchos['prom'] + $anchos['bruto'] + $anchos['tara'] + $anchos['neto'] + 
              $anchos['precio'], 8, 'TOTAL GENERAL', 0, 0, 'R');
    $pdf->Cell($anchos['total'], 8, 'S/ ' . number_format($totalGeneral, 2), 0, 0, 'R');
    $pdf->Cell($anchos['pago'], 8, 'S/ ' . number_format($totalPagado, 2), 0, 0, 'R');
    $pdf->Cell($anchos['saldo'], 8, 'S/ ' . number_format($totalSaldo, 2), 0, 1, 'R');
    
    // Panel de conversión a dólares (modificado para mostrar en soles primero)
    if (!empty($filtros['tasa_cambio']) && is_numeric($filtros['tasa_cambio']) && $filtros['tasa_cambio'] > 0) {
        $pdf->Ln(8);
        $y_pos = $pdf->GetY();
        
        // Panel con sombra sutil
        $pdf->SetFillColor(248, 249, 252);
        $pdf->RoundedRect(10, $y_pos, 277, 22, 2, 'F');
        $pdf->SetDrawColor($pdf->colorBorde[0], $pdf->colorBorde[1], $pdf->colorBorde[2]);
        $pdf->RoundedRect(10, $y_pos, 277, 22, 2, 'D');
        
        // Título del panel
        $pdf->SetTextColor($pdf->colorPrimario[0], $pdf->colorPrimario[1], $pdf->colorPrimario[2]);
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY(15, $y_pos + 3);
        $pdf->Cell(0, 6, utf8_decode('CONVERSIÓN MONETARIA (Tasa: ' . number_format($filtros['tasa_cambio'], 4) . ')'), 0, 1);
        
        // Línea divisoria
        $pdf->SetDrawColor(220, 223, 228);
        $pdf->Line(15, $y_pos + 10, 282, $y_pos + 10);
        
        // Montos en Soles (primero)
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor(70, 85, 100);
        $pdf->SetXY(15, $y_pos + 12);
        $pdf->Cell(0, 5, 'MONTOS EN SOLES (S/):', 0, 1);
        
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetXY(25, $y_pos + 17);
        $pdf->Cell(85, 5, 'Total Ventas: S/ ' . number_format($totalGeneral, 2), 0, 0);
        $pdf->Cell(85, 5, 'Total Pagado: S/ ' . number_format($totalPagado, 2), 0, 0);
        $pdf->Cell(0, 5, 'Saldo Pendiente: S/ ' . number_format($totalSaldo, 2), 0, 1);
        
    }
    
    // Nota final con estilo moderno
    $pdf->Ln(10);
    $y_pos = $pdf->GetY();
    
    $pdf->SetFillColor(240, 242, 245);
    $pdf->RoundedRect(10, $y_pos, 277, 8, 1, 'F');
    $pdf->SetDrawColor($pdf->colorBorde[0], $pdf->colorBorde[1], $pdf->colorBorde[2]);
    $pdf->RoundedRect(10, $y_pos, 277, 8, 1, 'D');
    
    $pdf->SetTextColor(120, 134, 156);
    $pdf->SetFont('Helvetica', 'I', 8);
    $pdf->SetXY(15, $y_pos + 2);
    $pdf->Cell(0, 4, utf8_decode('📊 Reporte generado el ') . date('d/m/Y H:i') . 
               utf8_decode(' • Todos los montos están expresados en Soles (S/)'), 0, 1);
    
    $pdf->Output('Reporte_Ventas_Cliente_' . date('Ymd_His') . '.pdf', 'I');
} else {
    die("No se encontraron configuraciones para mostrar.");
}
?>