import { mostrarClientesSelect } from "./ventas.js";

/* ======================================
OBTENIENDO LA SESSION DEL USUARIO
====================================== */
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

/* ===========================================
GUARDAR CLIENTE
=========================================== */
$("#btn_guardar_cliente").click(function (e) {

  e.preventDefault();

  var isValid = true;

  var tipo_persona = $("#tipo_personas_c").val();
  var razon_social = $("#razon_social_c").val();
  var id_doc = $("#id_doc_c").val();
  var numero_documento = $("#numero_documento_c").val();
  var direccion = $("#direccion_c").val();
  var ciudad = $("#ciudad_c").val();
  var codigo_postal = $("#codigo_postal_c").val();
  var telefono = $("#telefono_c").val();
  var email = $("#correo_c").val();
  var sitio_web = $("#sitio_web_c").val();
  var tipo_banco = $("#tipo_banco_c").val();
  var numero_cuenta = $("#numero_cuenta_c").val();


  // Validar el nombre de cliente
  if (razon_social === "") {
    $("#error_razon_social_c")
      .html("Por favor, ingrese la razón social")
      .addClass("text-danger");
    isValid = false;
  } else if (!isNaN(razon_social)) {
    $("#error_razon_social_c")
      .html("La razón social no puede contener números")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_razon_social_c").html("").removeClass("text-danger");
  }

  // Validar el tipo de documento
  if (id_doc === "" || id_doc == null) {
    $("#error_id_doc_c")
      .html("Por favor, selecione el tipo de documento")
      .addClass("text-danger");
    isValid = false;
  } else if (isNaN(id_doc)) {
    $("#error_id_doc_c")
      .html("El tipo de documento solo puede contener números")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_id_doc_c").html("").removeClass("text-danger");
  }

  // Validar el número de documento
  if (numero_documento === "") {
    $("#error_numero_documento_c")
      .html("Por favor, ingrese el número de documento")
      .addClass("text-danger");
    isValid = false;
  } else if (!/^\d{1,11}$/.test(numero_documento)) {
    $("#error_numero_documento_c")
      .html("El número de documento debe contener solo 11 dígitos")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_numero_documento_c").html("").removeClass("text-danger");
  }

  // Si el formulario es válido, envíalo
  if (isValid) {
    var datos = new FormData();
    datos.append("tipo_persona", tipo_persona);
    datos.append("razon_social", razon_social);
    datos.append("id_doc", id_doc);
    datos.append("numero_documento", numero_documento);
    datos.append("direccion", direccion);
    datos.append("ciudad", ciudad);
    datos.append("codigo_postal", codigo_postal);
    datos.append("telefono", telefono);
    datos.append("email", email);
    datos.append("sitio_web", sitio_web);
    datos.append("tipo_banco", tipo_banco);
    datos.append("numero_cuenta", numero_cuenta);

    $.ajax({
      url: "ajax/Cliente.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        var res = JSON.parse(respuesta);

        if (res === "ok") {
          if ($("#form_nuevo_cliente").length > 0) {
            $("#form_nuevo_cliente")[0].reset();
          }

          if ($("#form_nuevo_cliente_cotizacion").length > 0) {
            $("#form_nuevo_cliente_cotizacion")[0].reset();
          }
        
          $("#modalNuevoCliente").modal("hide");
          $("#modal_nuevo_cliente_cotizacion").modal("hide");
          Swal.fire({
            title: "¡Correcto!",
            text: "El cliente ha sido guardado",
            icon: "success",
          });
          mostrarClientes();
          mostrarClientesSelect();
        } else {
          console.error("Error al cargar los datos.");
        }
      },
    });
  }
});

/* ===========================
MOSTRANDO CLIENTES
=========================== */
async function mostrarClientes() {
  let sesion = await obtenerSesion();
  if(!sesion) return; // Si no hay sesión, salir de la función
  $.ajax({
    url: "ajax/Cliente.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (clientes) {
      var tbody = $("#dataClientes");
      tbody.empty();

      clientes.forEach(function (cliente, index) {
        if (cliente.tipo_persona == "cliente") {
          // Reemplazar valores vacíos con "No tiene" y agregar clase para opacidad
          let razonSocial = cliente.razon_social ? cliente.razon_social : '<span class="text-secondary">No tiene</span>';
          let nombreDoc = cliente.nombre_doc ? cliente.nombre_doc : '<span class="text-secondary">No tiene</span>';
          let numeroDoc = cliente.numero_documento ? cliente.numero_documento : '<span class="text-secondary">No tiene</span>';
          let direccion = cliente.direccion ? cliente.direccion : '<span class="text-secondary">No tiene</span>';
          let telefono = cliente.telefono ? cliente.telefono : '<span class="text-secondary">No tiene</span>';
          let email = cliente.email ? cliente.email : '<span class="text-secondary">No tiene</span>';

          var fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${razonSocial}</td>
                            <td class="text-center">
                                <span>${nombreDoc}:</span><br>
                                <span>${numeroDoc}</span>
                            </td>
                            <td>${direccion}</td>
                            <td>${telefono}</td>
                            <td>${email}</td>
                            <td>
                            ${sesion.permisos.clientes && sesion.permisos.clientes.acciones.includes("estado")?
                              `${cliente.estado_persona != 0 ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idCliente="' + cliente.id_persona + '" estadoCliente="0">Activado</button>' : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idCliente="' + cliente.id_persona + '" estadoCliente="1">Desactivado</button>'}`:``}
                            </td>
                            <td class="text-center">
                                ${sesion.permisos.clientes && sesion.permisos.clientes.acciones.includes("editar")?
                                  `<a href="#" class="me-3 btnEditarCliente" idCliente="${cliente.id_persona}" data-bs-toggle="modal" data-bs-target="#modalEditarCliente">
                                    <i class="text-warning fas fa-edit fa-lg"></i>
                                  </a>`:``}
                                
                                ${sesion.permisos.clientes && sesion.permisos.clientes.acciones.includes("ver")?
                                  `<a href="#" class="me-3 btnVerCliente" idVerCliente="${cliente.id_persona}" data-bs-toggle="modal" data-bs-target="#modalVerCliente">
                                    <i class="text-primary fa fa-eye fa-lg"></i>
                                  </a>`:``}
                                
                                ${sesion.permisos.clientes && sesion.permisos.clientes.acciones.includes("eliminar")?
                                  `<a href="#" class="me-3 confirm-text btnEliminarCliente" idCliente="${cliente.id_persona}">
                                    <i class="text-danger fa fa-trash fa-lg"></i>
                                  </a>`:``}
                                
                            </td>
                        </tr>
                    `;
          tbody.append(fila);
        }
      });

      // Inicializar DataTables después de cargar los datos
      $('#tabla_clientes').DataTable();
    },
    error: function (xhr, status, error) {
      console.error("Error al recuperar los proveedores:", error);
    },
  });
}


/*=============================================
ACTIVAR CLIENTE
=============================================*/
$("#tabla_clientes").on("click", ".btnActivar", function () {

  var idCliente = $(this).attr("idCliente");
  var estadoCliente = $(this).attr("estadoCliente");

  var datos = new FormData();
  datos.append("activarId", idCliente);
  datos.append("activarCliente", estadoCliente);

  $.ajax({
    url: "ajax/Cliente.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    success: function (respuesta) {
      if (window.matchMedia("(max-width:767px)").matches) {
        swal({
          title: "El usuario ha sido actualizado",
          type: "success",
          confirmButtonText: "¡Cerrar!",
        }).then(function (result) {
          if (result.value) {
            window.location = "usuarios";
          }
        });
      }
    },
  });

  if (estadoCliente == 0) {
    $(this)
      .removeClass("bg-lightgreen")
      .addClass("bg-lightred")
      .html("Desactivado");
    $(this).attr("estadoCliente", 1);
  } else {
    $(this)
      .removeClass("bg-lightred")
      .addClass("bg-lightgreen")
      .html("Activado");
    $(this).attr("estadoCliente", 0);
  }

});

/*=============================================
EDITAR EL CLIENTE
=============================================*/
$("#tabla_clientes").on("click", ".btnEditarCliente", function (e) {
  e.preventDefault();
  var idCliente = $(this).attr("idCliente");
  const datos = new FormData();
  datos.append("idCliente", idCliente);

  $.ajax({
    url: "ajax/Cliente.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("#edit_id_c").val(respuesta["id_persona"]);
      $("#edit_razon_social_c").val(respuesta["razon_social"]);
      $("#edit_id_doc_c").val(respuesta["id_doc"]);
      $("#edit_numero_documento_c").val(respuesta["numero_documento"]);
      $("#edit_direccion_c").val(respuesta["direccion"]);
      $("#edit_ciudad_c").val(respuesta["ciudad"]);
      $("#edit_codigo_postal_c").val(respuesta["codigo_postal"]);
      $("#edit_telefono_c").val(respuesta["telefono"]);
      $("#edit_correo_c").val(respuesta["email"]);
      $("#edit_sitio_web_c").val(respuesta["sitio_web"]);
      $("#edit_tipo_banco_c").val(respuesta["tipo_banco"]);
      $("#edit_numero_cuenta_c").val(respuesta["numero_cuenta"]);
    },
  });
});


/*=============================================
VER CLIENTE
=============================================*/
$("#tabla_clientes").on("click", ".btnVerCliente", function (e) {
  e.preventDefault();
  let idVerCliente = $(this).attr("idVerCliente");
  const datos = new FormData();
  datos.append("idVerCliente", idVerCliente);
  $.ajax({
    url: "ajax/Cliente.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#ver_razon_social_p").text(respuesta["razon_social"]);
      $("#ver_tipo_documento_p").text(respuesta["nombre_doc"]);
      $("#ver_numero_documento_p").text(respuesta["numero_documento"]);
      $("#ver_direccion_p").text(respuesta["direccion"]);
      $("#ver_ciudad_p").text(respuesta["ciudad"]);
      $("#ver_codigo_postal_p").text(respuesta["codigo_postal"]);
      $("#ver_telefono_p").text(respuesta["telefono"]);
      $("#ver_correo_p").text(respuesta["email"]);
      $("#ver_sitio_web_p").attr("href", respuesta["sitio_web"]).text("Visite el sitio web");
      $("#ver_tipo_banco_p").val(respuesta["tipo_banco"]);
      $("#ver_numero_cuenta_p").text(respuesta["numero_cuenta"]);
    },
  });
});


/*===========================================
ACTUALIZAR CLIENTE
=========================================== */
$("#btn_actualizar_cliente").click(function (e) {
  e.preventDefault();
  var isValid = true;
  var edit_id_cliente = $("#edit_id_c").val();
  var edit_razon_social = $("#edit_razon_social_c").val();
  var edit_id_doc = $("#edit_id_doc_c").val();
  var edit_numero_documento = $("#edit_numero_documento_c").val();
  var edit_direccion = $("#edit_direccion_c").val();
  var edit_ciudad = $("#edit_ciudad_c").val();
  var edit_codigo_postal = $("#edit_codigo_postal_c").val();
  var edit_telefono = $("#edit_telefono_c").val();
  var edit_email = $("#edit_correo_c").val();
  var edit_sitio_web = $("#edit_sitio_web_c").val();
  var edit_tipo_banco = $("#edit_tipo_banco_c").val();
  var edit_numero_cuenta = $("#edit_numero_cuenta_c").val();

  // Validar el nombre de cliente
  if (edit_razon_social === "") {
    $("#edit_error_rz_c")
      .html("Por favor, ingrese la razón social")
      .addClass("text-danger");
    isValid = false;
  } else if (!isNaN(edit_razon_social)) {
    $("#edit_error_rz_c")
      .html("La razón social no puede contener números")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#edit_error_rz_c").html("").removeClass("text-danger");
  }

  // Validar el tipo de documento
  if (edit_id_doc === "" || edit_id_doc == null) {
    $("#edit_error_id_doc_c")
      .html("Por favor, selecione el tipo de documento")
      .addClass("text-danger");
    isValid = false;
  } else if (isNaN(edit_id_doc)) {
    $("#edit_error_id_doc_c")
      .html("El tipo de documento solo puede contener números")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#edit_error_id_doc_c").html("").removeClass("text-danger");
  }

  // Validar el número de documento
  if (edit_numero_documento === "") {
    $("#edit_error_nd_c")
      .html("Por favor, ingrese el número de documento")
      .addClass("text-danger");
    isValid = false;
  } else if (!/^\d{1,11}$/.test(edit_numero_documento)) {
    $("#edit_error_nd_c")
      .html("El número de documento debe contener solo 11 dígitos")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#edit_error_nd_c").html("").removeClass("text-danger");
  }

  if (isValid) {
    var datos = new FormData();
    datos.append("edit_id_cliente", edit_id_cliente);
    datos.append("edit_razon_social", edit_razon_social);
    datos.append("edit_id_doc", edit_id_doc);
    datos.append("edit_numero_documento", edit_numero_documento);
    datos.append("edit_direccion", edit_direccion);
    datos.append("edit_ciudad", edit_ciudad);
    datos.append("edit_codigo_postal", edit_codigo_postal);
    datos.append("edit_telefono", edit_telefono);
    datos.append("edit_email", edit_email);
    datos.append("edit_sitio_web", edit_sitio_web);
    datos.append("edit_tipo_banco", edit_tipo_banco);
    datos.append("edit_numero_cuenta", edit_numero_cuenta);

    $.ajax({
      url: "ajax/Cliente.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        var res = JSON.parse(respuesta);

        if (res === "ok") {
          $("#form_edit_cliente")[0].reset();

          $("#modalEditarCliente").modal("hide");
          Swal.fire({
            title: "¡Correcto!",
            text: "El cliente ha sido actualizado",
            icon: "success",
          });
          mostrarClientes();
        } else {
          console.error("Error al cargar los datos.");
        }
      }
    });
  }
});

/*=============================================
  ELIMINAR CLIENTE
  =============================================*/
$("#tabla_clientes").on("click", ".btnEliminarCliente", function (e) {

  e.preventDefault();

  var deleteIdCliente = $(this).attr("idCliente");

  var datos = new FormData();
  datos.append("deleteIdCliente", deleteIdCliente);

  Swal.fire({
    title: "¿Está seguro de borrar el cliente?",
    text: "¡Si no lo está puede cancelar la accíón!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#0084FF",
    cancelButtonColor: "#F1666D",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Si, borrar!",
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        url: "ajax/Cliente.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          var res = JSON.parse(respuesta);

          if (res === "ok") {

            Swal.fire({
              title: "¡Eliminado!",
              text: "El cliente ha sido eliminado",
              icon: "success",
            });

            mostrarClientes();

          } else {

            console.error("Error al eliminar los datos");

          }
        }
      });

    }
  });
}
);

/* =====================================
MSOTRANDO DATOS
===================================== */
mostrarClientes();


/* =========================================
MOSTRANDO REPORTE DE CLIENTES
============================================ */

function mostrarReporteClientes() {
  $.ajax({
    url: "ajax/Cliente.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (clientes) {

      let tbody = $("#dataReporteCliente");

      tbody.empty();

      let contador = 1;

      clientes.forEach(function (cliente) {

        let fila = `
                <tr>
                    <td>
                        ${contador}
                    </td>
                    <td>${cliente.razon_social}</td>
                    <td>
                        <span>${cliente.nombre_doc}: </span>
                        <span>${cliente.numero_documento}</span>
                    </td>
                    <td>
                        <span>${cliente.telefono} </span>
                    </td>
                    <td>
                        <span>${cliente.email} </span>
                    </td>
                    <td>
                        ${cliente.estado_persona != 0 ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idUsuario="' + cliente.id_persona + '" estadoUsuario="0">Activado</button>'
            : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idUsuario="' + cliente.id_persona + '" estadoUsuario="1">Desactivado</button>'
          }
                    </td>
                    
                </tr>`;


        // Agregar la fila al tbody

        tbody.append(fila);

        contador++;

      });

      // Inicializar DataTables después de cargar los datos

      $('#tabla_reporte_cliente').DataTable();

    },
    error: function (xhr, status, error) {

      console.error("Error al recuperar los usuarios:", error);

    },
  });
}

mostrarReporteClientes();


/*=============================================
DESCARGAR REPORTE DE CLIENTES
=============================================*/

$("#btn_descargar_reporte_cliente").click(function (e) {


  e.preventDefault();

  let idCliente = $(this).attr("idVenta");

  window.open("extensiones/reportes/reporte.cliente.php?idCliente=" + idCliente, "_blank");

});
