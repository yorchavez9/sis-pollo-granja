<?php
session_start();

include "modulos/layouts/head.php";
$rolesPermitidos = [
    "ADMINISTRADOR",
    "GERENTE",
    "SUPERVISOR",
    "VENDEDOR",
    "TRANSPORTISTA",
    "INVENTARIO",
    "CAJERO",
    "ALMACENERO",
    "AUXILIAR",
    "CONTADOR",
    "SOPORTE",
    "MARKETING",
    "CLIENTE",
    "PROVEEDOR",
    "COMPRADOR",
    "RECEPCIONISTA"
];

if (
    isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok" &&
    isset($_SESSION["roles"][0]["nombre"]) &&
    in_array($_SESSION["roles"][0]["nombre"], $rolesPermitidos)
) {
?>
    <?php echo '<div class="main-wrapper">'; ?>
    <div id="section_verficar_configuraciones">
        <script>
            $(document).ready(function() {
                // Función para mostrar o ocultar la alerta y la sección
                function verificarConfiguracion(url, elementId) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            const count = data.length;
                            if (count > 0) {
                                $(elementId).hide();
                            } else {
                                $(elementId).show();
                                // Mostrar la alerta solo si la sección está visible
                                if ($(elementId).is(":visible")) {
                                    Swal.fire({
                                        title: "¡Aviso!",
                                        text: "Por favor realice las ajustes o configuraciones correspondientes, caso contrario no podrá usar el sistema",
                                        icon: "warning"
                                    });
                                }
                            }
                        }
                    });
                }

                // Llamadas a la función para cada configuración
                verificarConfiguracion("ajax/Configuracion.ticket.ajax.php", "#section_verficar_configuraciones");
                /* verificarConfiguracion("ajax/Correo.config.ajax.php", "#section_verficar_configuraciones"); */
                verificarConfiguracion("ajax/Configuracion.num.serie.ajax.php", "#section_verficar_configuraciones");
            });

            

        </script>
    </div>


    <?php include "modulos/header.php" ?>

    <?php include "modulos/sidebar.php"; ?>

    <?php
    if (isset($_GET["ruta"])) {

        if (
            $_GET["ruta"] == "inicio" ||
            $_GET["ruta"] == "sucursales" ||
            $_GET["ruta"] == "tipoDocumento" ||
            $_GET["ruta"] == "usuarios" ||
            $_GET["ruta"] == "roles" ||
            $_GET["ruta"] == "permisos" ||
            $_GET["ruta"] == "proveedores" ||
            $_GET["ruta"] == "clientes" ||
            $_GET["ruta"] == "productos" ||
            $_GET["ruta"] == "codigoBarra" ||
            $_GET["ruta"] == "clientes" ||
            $_GET["ruta"] == "categorias" ||
            $_GET["ruta"] == "cotizacion" ||
            $_GET["ruta"] == "listaCotizaciones" ||
            $_GET["ruta"] == "ventas" ||
            $_GET["ruta"] == "compras" ||
            $_GET["ruta"] == "listaCompras" ||
            $_GET["ruta"] == "crear-venta" ||
            $_GET["ruta"] == "editar-venta" ||
            $_GET["ruta"] == "reportes" ||
            $_GET["ruta"] == "trabajador" ||
            $_GET["ruta"] == "contratoTrabajador" ||
            $_GET["ruta"] == "pagoTrabajador" ||
            $_GET["ruta"] == "vacaciones" ||
            $_GET["ruta"] == "asistencia" ||
            $_GET["ruta"] == "reporteSucursales" ||
            $_GET["ruta"] == "reporteUsuarios" ||
            $_GET["ruta"] == "reporteRoles" ||
            $_GET["ruta"] == "reporteProveedores" ||
            $_GET["ruta"] == "reporteClientes" ||
            $_GET["ruta"] == "reporteCategorias" ||
            $_GET["ruta"] == "reporteProductos" ||
            $_GET["ruta"] == "reporteCompras" ||
            $_GET["ruta"] == "reporteVentas" ||

            $_GET["ruta"] == "cajaGeneral" ||
            $_GET["ruta"] == "arqueosCaja" ||
            $_GET["ruta"] == "gastosIngresos" ||
            $_GET["ruta"] == "ingresoDiario" ||
            $_GET["ruta"] == "reportesCaja" ||

            $_GET["ruta"] == "reporteTrabajadores" ||
            $_GET["ruta"] == "reportePagosTrabajador" ||
            $_GET["ruta"] == "reporteAsistencia" ||
            $_GET["ruta"] == "configuracionTicket" ||
            $_GET["ruta"] == "configuracionImpresora" ||
            $_GET["ruta"] == "configuracionSistema" ||
            $_GET["ruta"] == "configuracionCorreo" ||
            $_GET["ruta"] == "numFolio" ||
            $_GET["ruta"] == "salir"
        ) {

            include "modulos/" . $_GET["ruta"] . ".php";
        } else {

            include "modulos/404.php";
        }
    } else {

        include "modulos/inicio.php";
    }


    ?>


    <?php echo '</div>'; ?>
<?php
} else {
    include "modulos/login.php";
}
?>

<?php include "modulos/layouts/footer.php"; ?>
