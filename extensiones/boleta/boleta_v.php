<?php
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

$nombre_usuario = $_SESSION["nombre_usuario"];

require_once "../../controladores/Ventas.controlador.php";
require_once "../../modelos/Ventas.modelo.php";

require_once "../../controladores/Producto.controlador.php";
require_once "../../modelos/Producto.modelo.php";

require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Configuracion.ticket.modelo.php";

function formatearPrecio($precio) {
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
$item = "id_venta";
$valor = $_GET["id_venta"];

$respuesta = ControladorVenta::ctrMostrarListaVentas($item, $valor);
$serieNumero = $respuesta["num_comprobante"];
$seriePrefijo = $respuesta["serie_prefijo"];
$horaVenta = $respuesta["hora_venta"];
$respuesta_dv = ControladorVenta::ctrMostrarDetalleVenta($item, $valor);
$fechaVenta = $respuesta["fecha_venta"];
$impuesto = $respuesta["impuesto"];
$horaFormateada = date("h:i A", strtotime($fechaVenta));

$itemConfig = null;
$valorConfig = null;

// Obtener configuración de la empresa
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

// Calcular totales
$totalCompra = 0;
foreach ($respuesta_dv as $producto) {
    $totalProducto = ($producto['peso_neto'] == 0) 
        ? $producto['numero_aves'] * $producto['precio_venta'] 
        : $producto['peso_neto'] * $producto['precio_venta'];
    $totalCompra += $totalProducto;
}
$impuestoTotal = $totalCompra * ($impuesto / 100);
$totalConImpuesto = $totalCompra + $impuestoTotal;

// Conversión a USD
$subTotalVES = $totalCompra * $currentRate;
$impuestoTotalVES = $impuestoTotal * $currentRate;
$TotalVES = $totalConImpuesto * $currentRate;

// HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 10pt; margin: 0; padding: 10px; color: #333; }
        .header { margin-bottom: 15px; }
        .factura-title { font-size: 18pt; font-weight: bold; margin-bottom: 5px; color: #007BFF; }
        .factura-info { font-size: 10pt; margin-bottom: 3px; }
        .logo { float: right; width: 30mm; height: auto; }
        .empresa-nombre { font-size: 14pt; font-weight: bold; text-align: center; margin-top: 5px; color: #007BFF; }
        .cliente-info { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #007BFF; color: #fff; font-weight: bold; text-align: center; padding: 5px; border: 1px solid #ddd; }
        td { padding: 5px; border: 1px solid #ddd; text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; }
        .footer { margin-top: 80px; text-align: center; font-size: 9pt; color: #777; }
        .page-number { font-style: italic; font-size: 8pt; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="data:image/png;base64,'.base64_encode(file_get_contents('../'.$logo)).'" />
        <div class="empresa-nombre">'.$nombreEmpresa.'</div>
    </div>

    <div class="factura-title">BOLETA</div>
    <div class="factura-info">Boleta N° '.$seriePrefijo.'-'.$serieNumero.'</div>
    <div class="factura-info">Fecha: '.date("d/m/Y", strtotime($fechaVenta)).'</div>
    <div class="factura-info">Hora: '.$horaVenta.'</div>

    <div class="cliente-info">
        <div><strong>Cliente:</strong> '.$respuesta["razon_social"].'</div>
        <div><strong>Documento:</strong> '.$respuesta["numero_documento"].'</div>
        <div><strong>Dirección:</strong> '.$respuesta["direccion"].'</div>
        <div><strong>Teléfono:</strong> '.$respuesta["telefono"].'</div>
        <div><strong>Correo:</strong> '.$respuesta["email"].'</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Prod.</th>
                <th>Javas</th>
                <th>Uni.</th>
                <th>Peso Prom.</th>
                <th>Peso Br.</th>
                <th>Peso Tara</th>
                <th>Merma</th>
                <th>Peso Neto</th>
                <th>P. Venta</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

// Productos
usort($respuesta_dv, function($a, $b) { return strcmp($a['nombre_producto'], $b['nombre_producto']); });
foreach ($respuesta_dv as $producto) {
    $totalProducto = ($producto['peso_neto'] == 0) 
        ? $producto['numero_aves'] * $producto['precio_venta'] 
        : $producto['peso_neto'] * $producto['precio_venta'];

    $html .= '
            <tr>
                <td class="text-left">'.$producto['nombre_producto'].'</td>
                <td>'.intval($producto['numero_javas']).'</td>
                <td>'.$producto['numero_aves'].'</td>
                <td>'.$producto['peso_promedio'].'</td>
                <td>'.$producto['peso_bruto'].'</td>
                <td>'.$producto['peso_tara'].'</td>
                <td>'.$producto['peso_merma'].'</td>
                <td>'.$producto['peso_neto'].'</td>
                <td>S/ '.number_format($producto['precio_venta'], 2).'</td>
                <td>S/ '.number_format($totalProducto, 2).'</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="totals">
        <div class="text-right"><strong>Subtotal:</strong> S/ '.number_format($totalCompra, 2).'</div>
        <div class="text-right">USD '.number_format($subTotalVES, 2).'</div>
        <div class="text-right"><strong>Impuesto ('.intval($impuesto).'%):</strong> S/ '.number_format($impuestoTotal, 2).'</div>
        <div class="text-right">USD '.number_format($impuestoTotalVES, 2).'</div>
        <div class="text-right" style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 0;">
            <strong>Total:</strong> S/ '.number_format($totalConImpuesto, 2).'
        </div>
        <div class="text-right" style="border-bottom: 1px solid #000; padding: 3px 0;">
            USD '.number_format($TotalVES, 2).'
        </div>
    </div>

    <div class="footer">
        <div style="font-weight: bold; margin-bottom: 10px;">Gracias por su preferencia</div>
        <div style="margin-bottom: 15px;">'.$mensaje.'</div>
        <div>'.$nombreEmpresa.' | RUC:'.$ruc.'</div>
        <div>'.$direccion.' | Tel:'.$telefono.'</div>
        <div>Correo:'.$correo.'</div>
    </div>
</body>
</html>';

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Salida del PDF
if (isset($_GET['accion']) && $_GET['accion'] === 'descargar') {
    $dompdf->stream("boleta_".$serieNumero.".pdf", array("Attachment" => true));
} else {
    $dompdf->stream("boleta_".$serieNumero.".pdf", array("Attachment" => false));
}
?>