<?php
require('../fpdf/fpdf.php');

session_start();

$nombre_usuario = $_SESSION["usuario"]["nombre_usuario"];

require_once "../../controladores/Cotizacion.controllador.php";
require_once "../../modelos/Cotizacion.modelo.php";

require_once "../../controladores/Producto.controlador.php";
require_once "../../modelos/Producto.modelo.php";

require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

function formatearPrecio($precio)
{
    return number_format($precio, 2, '.', ',');
}

function getExchangeRate() {
    $primaryUrl = 'https://api.exchangerate-api.com/v4/latest/PEN';
    $backupUrl = 'https://open.er-api.com/v6/latest/PEN';

    $response = file_get_contents($primaryUrl);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['rates']['USD'])) {
            return $data['rates']['USD'];
        }
    }

    $responseBackup = file_get_contents($backupUrl);
    if ($responseBackup !== false) {
        $dataBackup = json_decode($responseBackup, true);
        if (isset($dataBackup['rates']['USD'])) {
            return $dataBackup['rates']['USD'];
        }
    }

    return null;
}
$currentRate = getExchangeRate();


/* ========================================
MOSTRANDO DATOS DE LA VENTA
======================================== */
$item = "id_cotizacion";
$valor = $_GET["id_cotizacion"];

$respuesta = ControladorCotizacion::ctrMostrarListaCotizaciones($item, $valor);
$horaCotizacion = $respuesta["hora_cotizacion"];
$respuesta_dc = ControladorCotizacion::ctrMostrarDetalleCotizacion($item, $valor);
$fechaCotizacion = $respuesta["fecha_cotizacion"];
$numeroCotizacion = $respuesta["id_cotizacion"];
$impuesto = $respuesta["impuesto"];  // Obtenemos el valor del impuesto
$horaFormateada = date("h:i A", strtotime($fechaCotizacion));

$itemConfig = null;
$valorConfig = null;

// Obtener configuración de la empresa (datos de la tabla 'configuracion_ticket')
$configuracion = ControladorConfiguracionTicket::ctrMostrarConfiguracionTicket($itemConfig, $valorConfig);
foreach ($configuracion as $key => $value) {
    $nombreEmpresa = $value['nombre_empresa'];
    $ruc = $value['ruc'];
    $telefono = $value['telefono'];
    $correo = $value['correo'];
    $direccion = $value['direccion'];
    $logo = $value['logo'];
    $mensaje = $value['mensaje'];
}
class PDF extends FPDF
{

    private $numeroCotizacion;
    private $fechaCotizacion;
    private $horaCotizacion;
    private $nombreEmpresa;
    private $ruc;
    private $telefono;
    private $correo;
    private $direccion;
    private $logo;
    private $mensaje;

    // Constructor que acepta los datos de la empresa y los de la venta
    function __construct($numeroCotizacion, $fechaCotizacion, $horaCotizacion, $nombreEmpresa, $ruc, $telefono, $correo, $direccion, $logo, $mensaje)
    {
        parent::__construct(); // Llamada al constructor de la clase FPDF
        $this->numeroCotizacion = $numeroCotizacion;
        $this->fechaCotizacion = $fechaCotizacion;
        $this->horaCotizacion = $horaCotizacion;
        $this->nombreEmpresa = $nombreEmpresa;
        $this->ruc = $ruc;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
        $this->logo = $logo;
        $this->mensaje = $mensaje;
    }

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);

        // Información de la factura
        $this->SetX(10);
        $this->Cell(0, 7, 'BOLETA COTIZACION', 0, 1, 'L');
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(0, 7, utf8_decode('Cotización N°'. $this->numeroCotizacion), 0, 1, 'L');  // Accede a la propiedad
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 7, 'Fecha: ' . date("d/m/Y", strtotime($this->fechaCotizacion)), 0, 1, 'L');
        $this->Cell(0, 7, utf8_decode('Hora: ' . $this->horaCotizacion), 0, 1, 'L');
        $this->Ln(5);

        // Logotipo
        $this->SetX(170);
        $this->Image('../' . $this->logo, 158, 5, 30);
        $this->SetFont('Arial', 'B', 14);  // Establece la fuente 'Arial', en negrita ('B'), con tamaño 16
        $this->Cell(10, 0, utf8_decode($this->nombreEmpresa), 0, 1, 'C');
    }

    // Pie de página
    function Footer()
    {
        // Mensaje de agradecimiento
        $this->Ln(80);
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 10, utf8_decode('Gracias por su preferencia'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 9);
        $this->MultiCell(0, 7, utf8_decode($this->mensaje), 0, 'C');
        $this->Ln(5);

        // Información del negocio o empresa
        $this->SetFont('Helvetica', '', 9);
        $this->Cell(0, 7, utf8_decode($this->nombreEmpresa . ' | RUC:' . $this->ruc), 0, 1, 'C');
        $this->Cell(0, 7, utf8_decode($this->direccion . ' | Tel:' . $this->telefono), 0, 1, 'C');
        $this->Cell(0, 7, 'Correo:' . $this->correo, 0, 1, 'C');
        $this->Ln(5);

        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}


// Crear PDF
$pdf = new PDF($numeroCotizacion, $fechaCotizacion, $horaCotizacion, $nombreEmpresa, $ruc, $telefono, $correo, $direccion, $logo, $mensaje);
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
$pdf->Cell(0, 7, 'Cliente: ' . utf8_decode($respuesta["razon_social"]), 0, 1, 'L');
$pdf->Cell(0, 7, 'Documento: ' . $respuesta["numero_documento"], 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode('Dirección: ' . $respuesta["direccion"]), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode('Teléfono: ' . $respuesta["telefono"]), 0, 1, 'L');
$pdf->Cell(0, 7, utf8_decode('Correo: ' . $respuesta["email"]), 0, 1, 'L');
$pdf->Ln(5);

// Títulos de la tabla
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX(10);
$pdf->Cell(20, 10, 'Prod.', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Javas', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Uni.', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Prom.', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Br.', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Tara', 'T B', 0, 'C', true);
$pdf->Cell(15, 10, 'Merma', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'Peso Neto', 'T B', 0, 'C', true);
$pdf->Cell(20, 10, 'P. Venta', 'T B', 0, 'C', true);
$pdf->Cell(25, 10, 'Total', 'T B', 1, 'C', true);

// Datos de productos
$pdf->SetFont('Helvetica', '', 10);

$subtotal = 0;
usort($respuesta_dc, fn($a, $b) => strcmp($a['nombre_producto'], $b['nombre_producto']));
$totalCompra = 0;

foreach ($respuesta_dc as $index => $producto) {
    // Verificar si el peso bruto es cero
    if ($producto['peso_neto'] == 0) {
        // Si el peso bruto es cero, sumar el número de aves por el precio
        $totalProducto = $producto['numero_aves'] * $producto['precio_venta'];
    } else {
        // Si el peso bruto no es cero, calcular con peso bruto por el precio
        $totalProducto = $producto['peso_neto'] * $producto['precio_venta'];
    }

    $totalCompra += $totalProducto;
    $borde = ($index === count($respuesta_dc) - 1) ? 'B' : 0;

    // Ajusta el ancho de las celdas según sea necesario
    $pdf->SetX(10);
    $pdf->Cell(35, 10, utf8_decode($producto['nombre_producto']), $borde, 0, 'L');  // Aumentar el ancho
    $pdf->Cell(3, 10, intval($producto['numero_javas']), $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['numero_aves'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['peso_promedio'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_bruto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_tara'], $borde, 0, 'C');
    $pdf->Cell(15, 10, $producto['peso_merma'], $borde, 0, 'C');
    $pdf->Cell(20, 10, $producto['peso_neto'], $borde, 0, 'C');
    $pdf->Cell(20, 10, 'S/ ' . number_format($producto['precio_venta'], 2), $borde, 0, 'C');
    $pdf->Cell(25, 10, 'S/ ' . number_format($totalProducto, 2), $borde, 1, 'C');
}

// Cálculo del impuesto y el total
$impuestoTotal = $totalCompra * ($impuesto / 100);
$totalConImpuesto = $totalCompra + $impuestoTotal;

$subTotalVES = $totalCompra * $currentRate;
$impuestoTotalVES = $impuestoTotal * $currentRate;
$TotalVES = $totalConImpuesto * $currentRate;

// Resumen del total
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 10);

$pdf->Cell(150, 10, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($totalCompra, 2), 0, 1, 'R');
$pdf->Cell(190, 10, 'USD ' . number_format($subTotalVES, 2), 0, 1, 'R');

$pdf->Cell(150, 10, 'Impuesto (' . intval($impuesto) . '%):', 0, 0, 'R');
$pdf->Cell(40, 10, 'S/ ' . number_format($impuestoTotal, 2), 0, 1, 'R');
$pdf->Cell(190, 10, 'USD ' . number_format($impuestoTotalVES, 2), 0, 1, 'R');

$pdf->Cell(150, 10, 'Total:', 'TB', 0, 'R'); // Borde superior e inferior en la celda de "Total"
$pdf->Cell(40, 10, 'S/ ' . number_format($totalConImpuesto, 2), 'TB', 1, 'R'); // Borde superior e inferior en la celda del total
$pdf->Cell(190, 10, 'USD ' . number_format($TotalVES, 2), 'TB', 1, 'R'); // Borde superior e inferior en la celda del total

/* ======================================================
GUARDANDO EL COMPROBANTE EN EL DIRECTORIO BOLETA
====================================================== */
$directorio = "boleta/cotizacion/";
$nombreArchivo = 'boleta_c_' . $numeroCotizacion . '.pdf';
$rutaArchivo = $directorio . $nombreArchivo;
if (!file_exists($rutaArchivo)) {
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $pdf->Output('F', $rutaArchivo);
} else {
    echo "El archivo PDF ya existe.";
}
