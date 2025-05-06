$(document).ready(function () {

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
  MOSTRANDO DATOS
  ===================================== */
  mostrarUsuarios();

  /* =====================================
  VISTA PREVIA DE LA IMAGEN DEL USUARIO
  ===================================== */
  $("#imagen_usuario").change(function () {
    const file = this.files[0];

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        $(".vistaPreviaImagenUsuario").attr("src", e.target.result);
        $(".vistaPreviaImagenUsuario").show();
      };

      reader.readAsDataURL(file);
    }
  });

  $("#edit_new_imagen_usuario").change(function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $(".edit_vista_imagen_usuario").attr("src", e.target.result);
        $(".edit_vista_imagen_usuario").show();
      };
      reader.readAsDataURL(file);
    }
  });

  /* =====================================
  VALIDANDO IMAGEN DEL USUARIO
  ===================================== */
  $("#imagen_usuario").change(function () {
    var imagen = $(this).get(0).files[0];

    if (imagen) {
      var maxSize = 5 * 1024 * 1024;

      if (imagen.size > maxSize) {
        Swal.fire({
          title: "¡Error!",
          text: "El tamaño de la imagen es demasiado grande. Por favor, seleccione una imagen más pequeña.",
          icon: "error",
        });
        $(this).val("");
        return;
      }

      var allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/jpg"];
      if (allowedTypes.indexOf(imagen.type) === -1) {
        Swal.fire({
          title: "¡Error!",
          text: "El tipo de archivo seleccionado no es válido. Por favor, seleccione una imagen en formato JPEG, PNG, GIF o JPG.",
          icon: "error",
        });
        $(this).val("");
        return;
      }
    } else {
      alert("Por favor, seleccione una imagen.");
    }
  });

  /* ===========================================
  GUARDAR USUARIO
  =========================================== */
  $("#guardar_usuario").click(() => {
    let isValid = true;
    const campos = {
      id_sucursal: $("#id_sucursal").val(),
      nombre_usuario: $("#nombre_usuario").val(),
      telefono: $("#telefono").val(),
      correo: $("#correo").val(),
      usuario: $("#usuario").val(),
      contrasena: $("#contrasena").val(),
      imagen: $("#imagen_usuario").get(0).files[0],
      action: "crear"
    };

    const validarCampo = (campo, mensaje, nombreLegible, regex = null) => {
      if (!campo || (regex && !regex.test(campo))) {
        $(`#error${mensaje}`).html(`Por favor, ingrese un ${nombreLegible} válido`).addClass("text-danger");
        isValid = false;
      } else {
        $(`#error${mensaje}`).html("").removeClass("text-danger");
      }
    };

    validarCampo(campos.id_sucursal, "id_sucursal", "Sucursal");
    validarCampo(campos.nombre_usuario, "nombre_usuario", "Nombre de Usuario");
    validarCampo(campos.usuario, "usuario", "Usuario");
    validarCampo(campos.telefono, "TelefonoUsuario", "Teléfono", /^[0-9]{9,15}$/);
    validarCampo(campos.correo, "CorreoUsuario", "Correo", /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/);
    validarCampo(campos.contrasena, "Contrasena", "Contraseña", /^.{6,}$/);

    if (isValid) {
      const datos = new FormData();
      Object.entries(campos).forEach(([key, value]) => {
        datos.append(key, value);
      });

      $.ajax({
        url: "ajax/Usuario.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: (respuesta) => {
          const res = JSON.parse(respuesta);
          if (res.status) {
            $("#nuevoUsuario")[0].reset();
            $(".vistaPreviaImagenUsuario").attr("src", "");
            $("#modalNuevoUsuario").modal("hide");

            Swal.fire({
              title: "¡Correcto!",
              text: res.message,
              icon: "success",
            });
            mostrarUsuarios();
          } else if(res.status === "warning") {
            Swal.fire({
              title: "¡Correcto!",
              text: res.message,
              icon: "warning",
            });
          }else{
            Swal.fire({
              title: "¡Error!",
              text: res.message,
              icon: "error",
            });
          }
        },
      });
    }
  });

  /* ===========================
  MOSTRANDO USUARIOS
  =========================== */
  async function mostrarUsuarios() {
    let sesion = await obtenerSesion();
    if (sesion === null) {
      window.location.href = "login";
      return;
    }
    $.ajax({
      url: "ajax/usuario.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status && response.data.length > 0) {
          var tbody = $("#dataUsuarios");
          tbody.empty();

          response.data.forEach(function (usuario) {
            let ruta_imagen= usuario.imagen_usuario
              ? "vistas/img/usuarios/"+usuario.imagen_usuario
              : "vistas/img/usuarios/default.png";

            var fila = `
              <tr>
                  <td>
                      <a href="javascript:void(0);" class="product-img">
                          <img src="${ruta_imagen}" alt="${usuario.nombre_usuario}">
                      </a>
                  </td>
                  <td>${usuario.nombre_sucursal}</td>
                  <td>${usuario.nombre_usuario}</td>
                  <td>${usuario.usuario}</td>
                  <td>${usuario.telefono}</td>
                  <td>${usuario.correo}</td>
                  <td>
                  ${sesion.permisos.usuarios && sesion.permisos.usuarios.acciones.includes("estado")?
                    `${usuario.estado != 0 
                        ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="0">Activado</button>'
                        : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="1">Desactivado</button>'}`:``}
                  </td>
                  <td>
                      ${sesion.permisos.usuarios && sesion.permisos.usuarios.acciones.includes("editar")? 
                        `<a href="#" class="me-3 btnEditarUsuario" idUsuario="${usuario.id_usuario}" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario">
                          <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>`:``}
                      
                      ${sesion.permisos.usuarios && sesion.permisos.usuarios.acciones.includes("ver")? 
                        `<a href="#" class="me-3 btnVerUsuario" idUsuario="${usuario.id_usuario}" data-bs-toggle="modal" data-bs-target="#modalVerUsuario">
                          <i class="text-primary fa fa-eye fa-lg"></i>
                        </a>`:``}
                      
                      ${sesion.permisos.usuarios && sesion.permisos.usuarios.acciones.includes("eliminar")? 
                        `<a href="#" class="me-3 confirm-text btnEliminarUsuario" idUsuario="${usuario.id_usuario}" fotoUsuario="${usuario.imagen_usuario}">
                            <i class="fa fa-trash fa-lg" style="color: #F52E2F"></i>
                        </a>`:``}
                  </td>
              </tr>`;
            tbody.append(fila);
          });
          $('#tabla_usuarios').DataTable();
        } else {
          $("#dataUsuarios").html(`
            <tr>
                <td colspan="8" class="text-center">No se encontraron usuarios registrados.</td>
            </tr>
          `);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error);
      },
    });
  }

  /*=============================================
  ACTIVAR USUARIO
  =============================================*/
  $("#tabla_usuarios").on("click", ".btnActivar", function () {
    var idUsuario = $(this).attr("idUsuario");
    var estadoUsuario = $(this).attr("estadoUsuario");

    var datos = new FormData();
    datos.append("id_usuario", idUsuario);
    datos.append("estado", estadoUsuario);
    datos.append("action", "cambiarEstado");
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        let res = JSON.parse(respuesta);
        if(res.status) {
          Swal.fire({
            title: "¡Correcto!",
            text: res.message,
            icon: "success",
          });
        }else{
          console.log(res.message);
        }
        
      }
    })

    if (estadoUsuario == 0) {
      $(this)
        .removeClass("bg-lightgreen")
        .addClass("bg-lightred")
        .html("Desactivado");
      $(this).attr("estadoUsuario", 1);
    } else {
      $(this)
        .removeClass("bg-lightred")
        .addClass("bg-lightgreen")
        .html("Activado");
      $(this).attr("estadoUsuario", 0);
    }
  })

  /*=============================================
  EDITAR EL USUARIO
  =============================================*/
  $("#tabla_usuarios").on("click", ".btnEditarUsuario", function () {
    let idUsuario = $(this).attr("idUsuario");
    let datos = new FormData();
    datos.append("id_usuario", idUsuario);
    datos.append("action", "editar");

    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
      if (respuesta.status && respuesta.data) {
        const data = respuesta.data;

        $("#edit_id_usuario").val(data.id_usuario);
        $("#edit_id_sucursal").append(
        '<option value="' +
        data.id_sucursal +
        '" selected>' +
        data.nombre_sucursal +
        "</option>"
        );
        $("#edit_nombre_usuario").val(data.nombre_usuario);
        $("#edit_telefono_usuario").val(data.telefono);
        $("#edit_correo_usuario").val(data.correo);
        $("#edit_usuario_usuario").val(data.usuario);
        $("#edit_password_actual").val(data.contrasena);

        const imagenUsuario = data.imagen_usuario.substring(3);

        if (data.imagen_usuario) {
        $(".edit_vista_imagen_usuario").attr("src", imagenUsuario);
        } else {
        $(".edit_vista_imagen_usuario").attr(
          "src",
          "vistas/img/usuarios/default.jpeg"
        );
        }

        $("#edit_imagen_actual_usuario").val(data.imagen_usuario);
      } else {
        console.error("Error: Datos no encontrados o respuesta inválida.");
      }
      },
      error: function (xhr, status, error) {
      console.error("Error al recuperar los datos del usuario:", error);
      console.log(xhr);
      console.log(status);
      },
    });
  });

  /*=============================================
  MOSTRAR DETALLES DEL USUARIO
  =============================================*/
  $("#tabla_usuarios").on("click", ".btnVerUsuario", function (e) {
    e.preventDefault();
    let idUsuarioVer = $(this).attr("idUsuario");
    const datos = new FormData();
    datos.append("id_usuario", idUsuarioVer);
    datos.append("action", "ver");
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
      if (respuesta.status && respuesta.data) {
        const data = respuesta.data;
        let EstadoUsuario = data.estado === 1 ? "Usuario activado" : "Usuario desactivado";

        $("#mostrar_nombre_usuario").text(data.nombre_usuario);
        $("#mostrar_nombre_sucursal").text(data.nombre_sucursal);
        $("#mostrar_telefono_usuario").text(data.telefono);
        $("#mostrar_correo_usuario").text(data.correo);
        $("#mostrar_usuario").text(data.usuario);
        $("#mostrar_estado_usuario").text(EstadoUsuario);

        const imagenUsuario = data.imagen_usuario
        ? `vistas/img/usuarios/${data.imagen_usuario}`
        : "vistas/img/usuarios/default/anonymous.png";

        $(".mostrarFotoUsuario").attr("src", imagenUsuario);
      } else {
        console.error("Error: Datos no encontrados o respuesta inválida.");
      }
      },
      error: function (xhr, status, error) {
      console.error("Error al recuperar los datos del usuario:", error);
      },
    });
  });

  /*===========================================
  ACTUALIZAR EL USUARIO
  =========================================== */
  $("#btn_update_usuario").click(function (e) {
    e.preventDefault();

    let edit_idSucursal = $("#edit_id_sucursal").val();
    let edit_nombreUsuario = $("#edit_nombre_usuario").val().trim();
    let edit_telefonoUsuario = $("#edit_telefono_usuario").val().trim();
    let edit_correoUsuario = $("#edit_correo_usuario").val().trim();
    let edit_usuarioUsuario = $("#edit_usuario_usuario").val().trim();
    let edit_contrasena = $("#edit_new_password_usuario").val();
    let edit_actualContrasena = $("#edit_password_actual").val();
    let edit_imagen = $("#edit_new_imagen_usuario")[0].files[0];
    let edit_imagenActualUsuario = $("#edit_imagen_actual_usuario").val();

    let isValid = true;

    $(".text-danger").text("");

    if (!edit_idSucursal) {
      $("#error_edit_id_sucursal").text("Debe seleccionar una sucursal.");
      isValid = false;
    }

    if (!edit_nombreUsuario || edit_nombreUsuario.length < 3) {
      $("#error_edit_nombre_usuario").text("Debe ingresar un nombre válido (mínimo 3 caracteres).");
      isValid = false;
    }

    if (!/^\d{9}$/.test(edit_telefonoUsuario)) {
      $("#error_edit_telefono_usuario").text("Debe ingresar un número de teléfono válido (9 dígitos).");
      isValid = false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(edit_correoUsuario)) {
      $("#error_edit_correo_usuario").text("Debe ingresar un correo electrónico válido.");
      isValid = false;
    }

    if (!edit_usuarioUsuario || edit_usuarioUsuario.length < 5) {
      $("#error_edit_usuario_usuario").text("El usuario debe tener al menos 5 caracteres.");
      isValid = false;
    }

    if (edit_contrasena && edit_contrasena.length < 6) {
      $("#edit_new_password_usuario").text("La contraseña debe tener al menos 6 caracteres.");
      isValid = false;
    }

    if (edit_imagen) {
      const allowedExtensions = ["jpg", "jpeg", "png", "gif"];
      const fileExtension = edit_imagen.name.split('.').pop().toLowerCase();
      if (!allowedExtensions.includes(fileExtension)) {
        $("#error_edit_new_imagen_usuario").text("El archivo debe ser una imagen (jpg, jpeg, png, gif).");
        isValid = false;
      }
    }

    if (isValid) {
      var datos = new FormData();
      datos.append("id_usuario", $("#edit_id_usuario").val());
      datos.append("id_sucursal", edit_idSucursal);
      datos.append("nombre_usuario", edit_nombreUsuario);
      datos.append("telefono", edit_telefonoUsuario);
      datos.append("correo", edit_correoUsuario);
      datos.append("usuario", edit_usuarioUsuario);
      datos.append("contrasena", edit_contrasena);
      datos.append("edit_actualContrasena", edit_actualContrasena);
      datos.append("imagen_usuario", edit_imagen);
      datos.append("edit_imagenActualUsuario", edit_imagenActualUsuario);
      datos.append("action", "actualizar");
      $.ajax({
        url: "ajax/Usuario.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          console.log(respuesta);
          try {
            var res = JSON.parse(respuesta);
            if (res.status) {
              $("#form_editar_usuario")[0].reset();
              $(".edit_vista_imagen_usuario").attr("src", "");
              $("#modalEditarUsuario").modal("hide");

              Swal.fire({
                title: "¡Correcto!",
                text: "El usuario ha sido actualizado con éxito",
                icon: "success",
              });

              mostrarUsuarios();
            } else {
              Swal.fire({
                title: "Error",
                text: "Hubo un error al actualizar el usuario.",
                icon: "error",
              });
            }
          } catch (error) {
            console.error("Respuesta inesperada:", respuesta);
          }
          mostrarUsuarios();
        },
        error: function (xhr, status, error) {
          console.error("Error en la solicitud AJAX:", error);
          console.log(xhr);
          console.log(status);
        },
      });
    }
  });

  /*=============================================
  ELIMINAR USUARIO
  =============================================*/
  $("#tabla_usuarios").on("click", ".btnEliminarUsuario", function (e) {
    e.preventDefault();

    var deleteUserId = $(this).attr("idUsuario");
    var deletefotoUser = $(this).attr("fotoUsuario");

    var datos = new FormData();
    datos.append("id_usuario", deleteUserId);
    datos.append("ruta_imagen", deletefotoUser);
    datos.append("action", "eliminar");

    Swal.fire({
      title: "¿Está seguro de borrar el usuario?",
      text: "¡Si no lo está puede cancelar la accíón!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar",
      confirmButtonText: "Si, borrar!",
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: "ajax/Usuario.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function (respuesta) {
            let res = JSON.parse(respuesta);
            if (res.status) {
              Swal.fire({
                title: "¡Eliminado!",
                text: res.message,
                icon: "success",
              });
              mostrarUsuarios();
            } else {
              console.error("Error al eliminar los datos");
            }
          }
        });
      }
    });
  });

  /*===========================================
  LIMPIAR MODALES
  =========================================== */
  $(".btn_modal_ver_close_usuario").click(function () {
    $("#mostrar_roles").text('');
  });

  $(".btn_modal_editar_close_usuario").click(function () {
    $("#formEditUsuario")[0].reset();
  });

  /* ===========================
  MOSTRANDO REPORTE USUARIO
  =========================== */
  function mostrarReporteUsuarios() {
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (usuarios) {
        var tbody = $("#data_usuarios_reporte");
        tbody.empty();
        usuarios.data.forEach(function (usuario, index) {
          usuario.imagen_usuario = usuario.imagen_usuario
            ? usuario.imagen_usuario.substring(3)
            : "../vistas/img/usuarios/default.jpeg";
          var fila = `
        <tr>
            <td>${index + 1}</td>
            <td>${usuario.nombre_sucursal}</td>
            <td>${usuario.nombre_usuario}</td>
            <td>${usuario.usuario}</td>
            <td>${usuario.telefono}</td>
            <td>${usuario.correo}</td>
            <td>
                ${usuario.estado != 0
              ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="0">Activado</button>'
              : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="1">Desactivado</button>'
            }
            </td>
        </tr>`;
          tbody.append(fila);
        });
        $('#tabla_usuarios_reporte').DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error);
      },
    });
  }

  mostrarReporteUsuarios();

  /*=============================================
  DESCARGAR REPORTE
  =============================================*/
  $("#seccion_usuarios_reporte").on("click", ".reporte_usuarios_pdf", (e) => {
    e.preventDefault();
    const url = "extensiones/reportes/usuarios.php";
    window.open(url, "_blank");
  });

  /*=============================================
  DESCARGAR IMPRIMIR
  =============================================*/
  $("#seccion_usuarios_reporte").on("click", ".reporte_usuarios_printer", (e) => {
    e.preventDefault();
    const url = "extensiones/reportes/usuarios.php";
    const newWindow = window.open(url, "_blank");
    newWindow.onload = () => {
      newWindow.print();
    };
  });
});