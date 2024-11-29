$("#btn_aplicar_filtros").on("click", function(e){
    e.preventDefault();
    let filtro_categoria = $("#filtro_categoria").val();
    let filtro_estado = $("#filtro_estado").val();
    let filtro_precio_min = $("#filtro_precio_min").val();
    let filtro_precio_max = $("#filtro_precio_max").val();
    let filtro_fecha_desde = $("#filtro_fecha_desde").val();
    let filtro_fecha_hasta = $("#filtro_fecha_hasta").val();


    const datos = new FormData();
    datos.append("filtro_categoria", filtro_categoria);
    datos.append("filtro_estado", filtro_estado);
    datos.append("filtro_precio_min", filtro_precio_min);
    datos.append("filtro_precio_max", filtro_precio_max);
    datos.append("filtro_fecha_desde", filtro_fecha_desde);
    datos.append("filtro_fecha_hasta", filtro_fecha_hasta);

    mostrarProductos(datos);
    $('#tabla_productos').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarProductos(datos) {
    $.ajax({
        url: "ajax/Reporte.producto.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (productos) {
            let tbody = $("#data_productos_reporte");
            tbody.empty();

            productos.forEach(function (producto, index) {
                // Reemplazar valores vacíos
                let categoria = producto.nombre_categoria || '<span class="text-secondary">Sin categoría</span>';
                let nombre = producto.nombre_producto || '<span class="text-secondary">Sin nombre</span>';
                let precio = producto.precio_producto || '<span class="text-secondary">Sin precio</span>';
                let stock = producto.stock_producto !== null ? producto.stock_producto : '<span class="text-secondary">Sin stock</span>';
                let fechaVencimiento = producto.fecha_vencimiento || '<span class="text-secondary">No aplica</span>';
                let estado = producto.estado_producto === 1 ? 'Activo' : 'Inactivo';

                // Generar fila
                let fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${categoria}</td>
                        <td>${nombre}</td>
                        <td>S/ ${precio}</td>
                        <td>${stock}</td>
                        <td>${fechaVencimiento}</td>
                        <td>${estado}</td>
                    </tr>
                `;
                tbody.append(fila);
            });
            $('#tabla_productos_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los productos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarProductos(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_productos_reporte").on("click", ".reporte_productos_pdf", (e) => {
    e.preventDefault();

    // Obtenemos los valores de los filtros actuales
    let filtro_categoria = $("#filtro_categoria").val();
    let filtro_estado = $("#filtro_estado").val();
    let filtro_precio_min = $("#filtro_precio_min").val();
    let filtro_precio_max = $("#filtro_precio_max").val();
    let filtro_fecha_desde = $("#filtro_fecha_desde").val();
    let filtro_fecha_hasta = $("#filtro_fecha_hasta").val();

    // Construir la URL con los filtros como parámetros
    const url = `extensiones/reportes/productos.php?filtro_categoria=${filtro_categoria}&filtro_estado=${filtro_estado}&filtro_precio_min=${filtro_precio_min}&filtro_precio_max=${filtro_precio_max}&filtro_fecha_desde=${filtro_fecha_desde}&filtro_fecha_hasta=${filtro_fecha_hasta}`;

    // Abrir el reporte PDF con los filtros aplicados
    window.open(url, "_blank");
});
