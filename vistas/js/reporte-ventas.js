async function obtenerSesion() {
  try {
      const response = await fetch('ajax/sesion.ajax.php?action=sesion', {
          method: 'GET',
          headers: { 'Accept': 'application/json' },
          credentials: 'include'
      });
      
      if (!response.ok) throw new Error('Error en la respuesta del servidor');
      
      const data = await response.json();
      return data.status === false ? null : data;
      
  } catch (error) {
      console.error('Error al obtener sesión:', error);
      return null;
  }
}
 
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




$("#btn_aplicar_filtros_ventas").on("click", function (e) {
    e.preventDefault();
    let filtro_usuario_venta = $("#filtro_usuario_venta").val();
    let filtro_fecha_desde_venta = $("#filtro_fecha_desde_venta").val();
    let filtro_fecha_hasta_venta = $("#filtro_fecha_hasta_venta").val();
    let filtro_tipo_comprobante_venta = $("#filtro_tipo_comprobante_venta").val();
    let filtro_estado_pago_venta = $("#filtro_estado_pago_venta").val();
    let filtro_total_venta_min = $("#filtro_total_venta_min").val();
    let filtro_total_venta_max = $("#filtro_total_venta_max").val();


    const datos = new FormData();
    datos.append("filtro_usuario_venta", filtro_usuario_venta);
    datos.append("filtro_fecha_desde_venta", filtro_fecha_desde_venta);
    datos.append("filtro_fecha_hasta_venta", filtro_fecha_hasta_venta);
    datos.append("filtro_tipo_comprobante_venta", filtro_tipo_comprobante_venta);
    datos.append("filtro_estado_pago_venta", filtro_estado_pago_venta);
    datos.append("filtro_total_venta_min", filtro_total_venta_min);
    datos.append("filtro_total_venta_max", filtro_total_venta_max);
   
    mostrarProductos(datos);
    $('#tabla_ventas_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
async function mostrarProductos(datos) {
    await updateRate();
    $.ajax({
        url: "ajax/Reporte.venta.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (ventas) {
            let tbody = $("#data_ventas_reporte");
            tbody.empty();

            ventas.forEach(function (producto, index) {
                // Reemplazar valores vacíos
                let nombre_usuario = producto.nombre_usuario || '<span class="text-secondary">Sin categoría</span>';
                let razon_social = producto.razon_social || '<span class="text-secondary">Sin proveedor</span>';
                let fecha_venta = producto.fecha_venta || '<span class="text-secondary">Sin fecha</span>';
                let comprobante = producto.tipo_comprobante_sn || '<span class="text-secondary">Sin comprobante</span>';
                let serie = producto.serie_prefijo || '<span class="text-secondary">Sin serie</span>';
                let numero = producto.num_comprobante || '<span class="text-secondary">Sin número</span>';
                let total_venta = producto.total_venta || '<span class="text-secondary">0.00</span>';
                let total_pago = producto.total_pago || '<span class="text-secondary">0.00</span>';

                // Determinar el estado con color
                let estado =
                    producto.estado_pago === 'completado'
                        ? `<span class="text-success">Pagado</span>`
                        : `<span class="text-danger">Pendiente</span>`;
                        var venta_total = currentRate > 0 ? (producto.total_venta * currentRate).toFixed(2) : "N/A";
                        var venta_total_pago = currentRate > 0 ? (producto.total_pago * currentRate).toFixed(2) : "N/A";
                // Generar fila
                let fila = `
            <tr>
                <td>${index + 1}</td>
                <td>${razon_social}</td>
                <td>${nombre_usuario}</td>
                <td>${fecha_venta}</td>
                <td>${comprobante}</td>
                <td>${serie}-${numero}</td>
                <td>
                    <div>S/ ${total_venta}</div>
                    <div>USD ${venta_total}</div>
                </td>
                <td>
                    <div>S/ ${total_pago}</div>
                    <div>USD ${venta_total_pago}</div>
                </td>
                <td>${estado}</td>
            </tr>
        `;
                tbody.append(fila);
            });

            // Inicializar DataTable
            $('#tabla_ventas_reporte').DataTable();
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
$("#seccion_ventas_reporte").on("click", ".reporte_ventas_pdf", (e) => {
    e.preventDefault();

    // Obtenemos los valores de los filtros actuales
    const filtro_usuario_venta = $("#filtro_usuario_venta").val() || "";
    const filtro_fecha_desde_venta = $("#filtro_fecha_desde_venta").val() || "";
    const filtro_fecha_hasta_venta = $("#filtro_fecha_hasta_venta").val() || "";
    const filtro_tipo_comprobante_venta = $("#filtro_tipo_comprobante_venta").val() || "";
    const filtro_estado_pago_venta = $("#filtro_estado_pago_venta").val() || "";
    const filtro_total_venta_min = $("#filtro_total_venta_min").val() || "";
    const filtro_total_venta_max = $("#filtro_total_venta_max").val() || "";

    // Construir la URL con los filtros como parámetros
    const url = `extensiones/reportes/ventas.php?` +
        `filtro_usuario_venta=${encodeURIComponent(filtro_usuario_venta)}` +
        `&filtro_fecha_desde_venta=${encodeURIComponent(filtro_fecha_desde_venta)}` +
        `&filtro_fecha_hasta_venta=${encodeURIComponent(filtro_fecha_hasta_venta)}` +
        `&filtro_tipo_comprobante_venta=${encodeURIComponent(filtro_tipo_comprobante_venta)}` +
        `&filtro_estado_pago_venta=${encodeURIComponent(filtro_estado_pago_venta)}` +
        `&filtro_total_venta_min=${encodeURIComponent(filtro_total_venta_min)}` +
        `&filtro_total_venta_max=${encodeURIComponent(filtro_total_venta_max)}`;

    // Abrir el reporte PDF con los filtros aplicados
    window.open(url, "_blank");

    // Limpiar los campos de filtro después de abrir el reporte
    $("#filtro_usuario_venta").val("");
    $("#filtro_fecha_desde_venta").val("");
    $("#filtro_fecha_hasta_venta").val("");
    $("#filtro_tipo_comprobante_venta").val("");
    $("#filtro_estado_pago_venta").val("");
    $("#filtro_total_venta_min").val("");
    $("#filtro_total_venta_max").val("");
});
