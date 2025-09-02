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
    private $isFirstPage = true;
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
        
        $this->SetMargins(10, $this->isFirstPage ? 10 : 25, 10);
        $this->SetAutoPageBreak(true, 15);
        $this->SetFont('Helvetica', '', 10);
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
        if ($this->isFirstPage) {
            // Header completo para la primera página
            // Fondo moderno para el header con sombra
            $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);
            $this->Rect(0, 0, 297, 45, 'F');
            
            // Barra superior con degradado
            $this->LinearGradient(0, 0, 297, 10, $this->colorPrimario, [52, 152, 219]);
            
            // Logo con marco circular más pequeño
            if (file_exists("../../uploads/" . $this->config['logo'])) {
                $this->SetFillColor(255, 255, 255);
                $this->RoundedRect(263, 12, 18, 18, 9, 'F');
                $this->Image("../../uploads/" . $this->config['logo'], 265, 14, 14, 14);
            }
            
            // Información de empresa con estilo moderno
            $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
            $this->SetFont('Helvetica', 'B', 14);
            $this->SetXY(15, 12);
            $this->Cell(0, 7, ($this->config['nombre_empresa']), 0, 1, 'L');
            
            $this->SetFont('Helvetica', '', 9);
            $this->SetXY(15, 20);
            $info_empresa = 'RUC: ' . $this->config['ruc'] . '  |  ' . 
                           ($this->config['direccion']) . '  |  ' .
                           'Tel: ' . $this->config['telefono'];
            $this->Cell(0, 5, $info_empresa, 0, 1, 'L');
            
            // Título principal con diseño moderno
            $this->SetY(38);
            $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
            $this->SetFont('Helvetica', 'B', 16);
            $this->SetFont('Helvetica', 'B', 13);
            $this->Cell(0, 8, 'REPORTE DE VENTAS POR CLIENTE', 0, 1, 'C');
            $this->SetFont('Helvetica', 'B', 16); // Restaurar tamaño original
            
            // Línea decorativa
            $this->SetDrawColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
            $this->SetLineWidth(0.5);
            $this->Line(50, 48, 247, 48);
            
            // Información del cliente con estilo de tarjeta
            $this->SetY(55);
            if (!empty($this->razonSocial)) {
                $this->SetFillColor(245, 247, 250);
                $this->RoundedRect(15, 55, 267, 8, 2, 'F');
                $this->SetDrawColor($this->colorBorde[0], $this->colorBorde[1], $this->colorBorde[2]);
                $this->RoundedRect(15, 55, 267, 8, 2, 'D');
                
                $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
                $this->SetFont('Helvetica', 'B', 10);
                $this->SetXY(20, 57);
                $this->Cell(0, 5, 'CLIENTE: ' . ($this->razonSocial), 0, 1, 'L');
                $this->SetY(65);
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
                $this->SetFont('Helvetica', '', 8);
                $this->SetXY(20, $y_current + 1);
                $this->Cell(0, 4, 'FILTROS: ' . implode(' • ', $filtrosAplicados), 0, 0, 'L');
                $this->SetY($y_current + 8);
            }
            
            // Información de generación con estilo minimalista
            $y_current = $this->GetY();
            $this->SetTextColor(149, 165, 166);
            $this->SetFont('Helvetica', 'I', 8);
            $this->SetXY(220, $y_current);
            $this->Cell(0, 4, 'Generado por: ' . $this->nombreUsuario, 0, 1, 'R');
            $this->SetXY(220, $y_current + 4);
            $this->Cell(0, 4, date('d/m/Y H:i'), 0, 1, 'R');
            
            $this->Ln(8);
            
            // Marcar que ya no es la primera página
            $this->isFirstPage = false;
            $this->SetMargins(10, 25, 10); // Cambiar márgenes para páginas siguientes
        } else {
            // Header simplificado para páginas siguientes
            $this->SetFillColor(245, 247, 250);
            $this->Rect(0, 0, 297, 20, 'F');
            
            // Línea decorativa
            $this->SetDrawColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
            $this->SetLineWidth(0.5);
            $this->Line(10, 19, 287, 19);
            
            // Título simplificado
            $this->SetTextColor($this->colorPrimario[0], $this->colorPrimario[1], $this->colorPrimario[2]);
            $this->SetFont('Helvetica', 'B', 12);
            $this->SetXY(10, 8);
            $this->Cell(0, 5, 'REPORTE DE VENTAS POR CLIENTE - Continuación', 0, 1, 'L');
            
            // Información del cliente (si existe)
            if (!empty($this->razonSocial)) {
                $this->SetTextColor($this->colorSecundario[0], $this->colorSecundario[1], $this->colorSecundario[2]);
                $this->SetFont('Helvetica', '', 9);
                $this->SetXY(10, 13);
                $this->Cell(0, 5, 'Cliente: ' . ($this->razonSocial), 0, 1, 'L');
            }
            
            $this->SetY(25); // Posicionar después del header simplificado
        }
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
        $this->SetFont('Helvetica', 'I', 9);
        $this->Cell(0, 15, ('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
    
    // Sobrescribir AddPage para resetear isFirstPage
    function AddPage($orientation = '', $size = '', $rotation = 0) {
        parent::AddPage($orientation, $size, $rotation);
        // Después de la primera página, cambiar márgenes
        if ($this->PageNo() > 1) {
            $this->SetMargins(10, 25, 10);
        }
    }
}

if (count($configuraciones) > 0) {
    $configuracion = $configuraciones[0];
    
    $pdf = new PDF($configuracion, $nombre_usuario, $razon_social, $filtros['tasa_cambio'], $filtros);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Anchos optimizados para diseño moderno
    $anchos = [
        'fecha' => 20,
        'producto' => 45,
        'javas' => 15,
        'aves' => 15,
        'prom' => 18,
        'bruto' => 18,
        'tara' => 18,
        'neto' => 18,
        'precio' => 22,
        'total' => 22,
        'pago' => 22,
        'saldo' => 22
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
        'P.Bruto', 'P.Tara', 'P.Neto', 'P.Unit.', 
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
    $ventasProcesadas = [];
    
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
        
        // Solo sumar los totales si no hemos procesado esta venta antes
        if (!in_array($venta['id_venta'], $ventasProcesadas)) {
            $subtotalVenta += $venta['total_venta'];
            $subtotalPagado += $venta['total_pago'];
            $subtotalSaldo += $venta['saldo'];
            $totalGeneral += $venta['total_venta'];
            $totalPagado += $venta['total_pago'];
            $totalSaldo += $venta['saldo'];
            
            // Marcar venta como procesada
            $ventasProcesadas[] = $venta['id_venta'];
        }
        
        $currentVentaId = $venta['id_venta'];
        
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
        $pdf->Cell($anchos['producto'], 5, (substr($venta['nombre_producto'], 0, 25)), 0, 0, 'L');
        $pdf->Cell($anchos['javas'], 5, $venta['numero_javas'], 0, 0, 'C');
        $pdf->Cell($anchos['aves'], 5, $venta['numero_aves'], 0, 0, 'C');
        $pdf->Cell($anchos['prom'], 5, number_format($venta['peso_promedio'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['bruto'], 5, number_format($venta['peso_bruto'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['tara'], 5, number_format($venta['peso_tara'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['neto'], 5, number_format($venta['peso_neto'], 2), 0, 0, 'R');
        $pdf->Cell($anchos['precio'], 5, 'S/ ' . number_format($venta['precio_venta'], 2), 0, 0, 'R');
        
        // Calcular y mostrar el total por producto
        $totalProducto = $venta['precio_venta'] * $venta['peso_neto'];
        $pdf->Cell($anchos['total'], 5, 'S/ ' . number_format($totalProducto, 2), 0, 0, 'R');
        
        // Para pagado y saldo, muestra el valor proporcional o deja en blanco para productos individuales
        $pdf->Cell($anchos['pago'], 5, '', 0, 0, 'R');
        $pdf->Cell($anchos['saldo'], 5, '', 0, 1, 'R');
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
    
    // Panel de conversión a dólares
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
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetXY(15, $y_pos + 3);
        $pdf->Cell(0, 6, ('CONVERSIÓN MONETARIA (Tasa: ' . number_format($filtros['tasa_cambio'], 4) . ')'), 0, 1);
        
        // Línea divisoria
        $pdf->SetDrawColor(220, 223, 228);
        $pdf->Line(15, $y_pos + 10, 282, $y_pos + 10);
        
        // Montos en Soles
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
    $pdf->Cell(0, 4, ('📊 Reporte generado el ') . date('d/m/Y H:i') . 
               (' • Todos los montos están expresados en Soles (S/)'), 0, 1);
    
    $pdf->Output('Reporte_Ventas_Cliente_' . date('Ymd_His') . '.pdf', 'I');
} else {
    die("No se encontraron configuraciones para mostrar.");
}
?>