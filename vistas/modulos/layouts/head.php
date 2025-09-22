<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Sistema de gestión para tiendas de pollos, pescados y mariscos. Administra ventas, inventarios y distribución de manera eficiente.">
    <meta name="keywords" content="pollos, pescados, mariscos, tienda, distribución, ventas, administración, inventario, negocio, comercio, gestión">
    <meta name="author" content="Distribuidor de Pollos y Mariscos - Tienda Online">
    <meta name="robots" content="index, follow">

    <?php
    $item = null;
    $valor = null;
    $sistema = ControladorConfiguracionSistema::ctrMostrarConfiguracionSistema($item, $valor);

    if (!empty($sistema)) {
        $value = $sistema[0];
        $nombre = isset($value["nombre"]) && !empty($value["nombre"]) ? $value["nombre"] : 'Nombre Predeterminado';
        $favicon = isset($value["icon_pestana"]) ? substr($value["icon_pestana"], 3) : 'vistas/img/sistema/favicon.png';
    ?>
        <title><?php echo htmlspecialchars($nombre); ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($favicon); ?>">
    <?php
    } else {
    ?>
        <title>Apuuray</title>
        <link rel="shortcut icon" type="image/x-icon" href="vistas/img/sistema/favicon.png">
    <?php
    }

    ?>


    <link rel="stylesheet" href="vistas/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="vistas/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="vistas/assets/css/animate.css">
    <link rel="stylesheet" href="vistas/assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="vistas/assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="vistas/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="vistas/assets/css/style.css">
    <link rel="stylesheet" href="vistas/assets/bootstrap-icons/font/bootstrap-icons.min.css">
    <script src="vistas/assets/js/jquery-3.6.0.min.js"></script>

    <!-- Configuración global de zona horaria peruana para JavaScript -->
    <script>
    (function() {
        // Override del constructor Date para usar siempre America/Lima (GMT-5)
        const OriginalDate = Date;

        window.Date = function(...args) {
            if (args.length === 0) {
                // new Date() sin argumentos - usar hora de Lima
                const ahora = new OriginalDate();
                const utc = ahora.getTime() + (ahora.getTimezoneOffset() * 60000);
                const fechaLima = new OriginalDate(utc + (-5 * 3600000)); // GMT-5 para Lima

                // Override toISOString para que devuelva la fecha de Lima en formato ISO
                fechaLima.toISOString = function() {
                    const year = this.getFullYear();
                    const month = String(this.getMonth() + 1).padStart(2, '0');
                    const day = String(this.getDate()).padStart(2, '0');
                    const hours = String(this.getHours()).padStart(2, '0');
                    const minutes = String(this.getMinutes()).padStart(2, '0');
                    const seconds = String(this.getSeconds()).padStart(2, '0');
                    const ms = String(this.getMilliseconds()).padStart(3, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}.${ms}Z`;
                };

                return fechaLima;
            } else {
                // new Date() con argumentos - comportamiento normal
                return new OriginalDate(...args);
            }
        };

        // Copiar métodos estáticos
        Object.setPrototypeOf(window.Date, OriginalDate);
        Object.defineProperty(window.Date, 'prototype', {
            value: OriginalDate.prototype,
            writable: false
        });

        // Métodos estáticos importantes
        window.Date.now = OriginalDate.now;
        window.Date.parse = OriginalDate.parse;
        window.Date.UTC = OriginalDate.UTC;

        // Función helper para obtener fecha de Lima en formato YYYY-MM-DD
        window.getFechaLimaISO = function() {
            const ahora = new OriginalDate();
            const utc = ahora.getTime() + (ahora.getTimezoneOffset() * 60000);
            const fechaLima = new OriginalDate(utc + (-5 * 3600000));
            return fechaLima.toISOString().split('T')[0];
        };

        // Mostrar confirmación en consola
        console.log('✅ Zona horaria JavaScript configurada a Lima, Perú (GMT-5)');
        console.log('📅 Fecha/Hora actual:', new Date().toLocaleString('es-PE'));
        console.log('📅 Fecha ISO Lima:', new Date().toISOString().split('T')[0]);
    })();
    </script>

    <link rel="stylesheet" href="vistas/assets/sweetalert2/dist/sweetalert2.min.css">
    <script src="vistas/assets/sweetalert2/dist/sweetalert2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script src="vistas/assets/plugins/select2/js/select2.min.js"></script>
    <script src="vistas/assets/plugins/select2/js/custom-select.js"></script>
</head>

<body>
<?php echo date("d/m/Y"); ?>

    <!-- <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <div class="loader-section">
        <span class="loader"></span>
    </div>

    
 -->