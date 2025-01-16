<?php
function getExchangeRate() {
    $primaryUrl = 'https://api.exchangerate-api.com/v4/latest/USD';
    $backupUrl = 'https://open.er-api.com/v6/latest/USD';

    $response = file_get_contents($primaryUrl);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['rates']['VES'])) {
            return $data['rates']['VES'];
        }
    }

    $responseBackup = file_get_contents($backupUrl);
    if ($responseBackup !== false) {
        $dataBackup = json_decode($responseBackup, true);
        if (isset($dataBackup['rates']['VES'])) {
            return $dataBackup['rates']['VES'];
        }
    }

    return null;
}
$currentRate = getExchangeRate();

if ($currentRate !== null) {
    $totalUSD = 20;
    $precioBolivares = $totalUSD * $currentRate;

    echo "La tasa de cambio actual es: {$currentRate} VES por USD.<br>";
    echo "El precio en bolívares es: " . number_format($precioBolivares, 2) . " VES.";
} else {
    echo "No se pudo obtener la tasa de cambio. Por favor, inténtelo más tarde.";
}
?>
