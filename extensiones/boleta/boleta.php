<?php
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

$nombre_usuario = $_SESSION["nombre_usuario"];

require_once "../../controladores/Compra.controlador.php";
require_once "../../controladores/Producto.controlador.php";
require_once "../../controladores/Configuracion.ticket.controlador.php";
require_once "../../modelos/Compra.modelo.php";
require_once "../../modelos/Producto.modelo.php";
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
MOSTRANDO DATOS DE LA COMPRA
======================================== */
$item = "id_egreso";
$valor = $_GET["id_egreso"];

$respuesta = ControladorCompra::ctrMostrarCompras($item, $valor);
$prefijoComprobane = $respuesta["serie_comprobante"];
$numeroComprobante = $respuesta["num_comprobante"];
$horaEgreso = $respuesta["hora_egreso"];
$respuesta_de = ControladorCompra::ctrMostrarDetalleCompra($item, $valor);
$fechaEgreso = $respuesta["fecha_egre"];
$impuesto = $respuesta["impuesto"];
$horaFormateada = date("h:i A", strtotime($fechaEgreso));

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
foreach ($respuesta_de as $producto) {
    $totalProducto = ($producto['peso_neto'] == 0) 
        ? $producto['numero_aves'] * $producto['precio_compra'] 
        : $producto['peso_neto'] * $producto['precio_compra'];
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
        .boleta-title { font-size: 18pt; font-weight: bold; margin-bottom: 5px; color: #007BFF; }
        .boleta-info { font-size: 10pt; margin-bottom: 3px; }
        .logo { float: right; width: 30mm; height: auto; }
        .empresa-nombre { font-size: 14pt; font-weight: bold; text-align: center; margin-top: 5px; color: #007BFF; }
        .proveedor-info { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #007BFF; color: #fff; font-weight: bold; text-align: center; padding: 5px; border: 1px solid #ddd; }
        td { padding: 5px; border: 1px solid #ddd; text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; }
        .footer { margin-top: 80px; text-align: center; font-size: 9pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="data:image/png;base64,'.base64_encode(file_get_contents('../'.$logo)).'" />
        <div class="empresa-nombre">'.$nombreEmpresa.'</div>
    </div>

    <div class="boleta-title">BOLETA</div>
    <div class="boleta-info">Boleta N° '.$prefijoComprobane.'-'.$numeroComprobante.'</div>
    <div class="boleta-info">Fecha: '.date("d/m/Y", strtotime($fechaEgreso)).'</div>
    <div class="boleta-info">Hora: '.$horaEgreso.'</div>

    <div class="proveedor-info">
        <div><strong>Proveedor:</strong> '.$respuesta["razon_social"].'</div>
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
                <th>P. Compra</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

// Productos
usort($respuesta_de, function($a, $b) { return strcmp($a['nombre_producto'], $b['nombre_producto']); });
foreach ($respuesta_de as $producto) {
    $totalProducto = ($producto['peso_neto'] == 0) 
        ? $producto['numero_aves'] * $producto['precio_compra'] 
        : $producto['peso_neto'] * $producto['precio_compra'];

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
                <td>S/ '.number_format($producto['precio_compra'], 2).'</td>
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
    $dompdf->stream("boleta_".$prefijoComprobane."-".$numeroComprobante.".pdf", array("Attachment" => true));
} else {
    $dompdf->stream("boleta_".$prefijoComprobane."-".$numeroComprobante.".pdf", array("Attachment" => false));
}