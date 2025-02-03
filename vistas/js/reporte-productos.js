
 /* =====================================
  CONVERTIR DE DOLARES A 
  ===================================== */
  let currentRate = 0;

  async function getExchangeRate(){
    try {
      const response = await fetch('https://api.exchangerate-api.com/v4/latest/PEN');
      const data = await response.json();
      return data.rates.USD;
    } catch (error) {
      console.error('Error obteniendo tasas', error);
      try {
        const response = await fetch('https://open.er-api.com/v6/latest/PEN');
        const data = await response.json();
        return data.rates.USD;
      } catch (error2) {
        console.log("Error en API de respaldo:", error2);
        return null;
      }
    }
  }

  async function updateRate() {
    try {
      const rate = await getExchangeRate();
      if (rate) {
        currentRate = rate; // Asigna la tasa de cambio al valor global
        document.getElementById("error_moneda").textContent = "";
      }
    } catch (error) {
     /*  console.error("Error al actualizar la tasa:", error); */
    }
  }

  setInterval(updateRate, 60 * 60 * 1000);



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
async function mostrarProductos(datos) {
    await updateRate();
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
                var precio_bolivares = currentRate > 0 ? (producto.precio_producto * currentRate).toFixed(2) : "N/A";
                // Generar fila
                let fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${categoria}</td>
                        <td>${nombre}</td>
                        <td>
                            <div>S/ ${producto.precio_producto}</div>
                            <div>USD ${precio_bolivares}</div>
                        </td>
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
