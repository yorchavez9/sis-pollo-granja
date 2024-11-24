$(document).ready(function () {

  /* =====================================
 MSOTRANDO DATOS
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

  /* =====================================
  VISTA PREVIA DE LA IMAGEN DEL USUARIO
  ===================================== */
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
      imagen: $("#imagen_usuario").get(0).files[0]
    };

    // Función de validación genérica con nombre específico
    const validarCampo = (campo, mensaje, nombreLegible, regex = null) => {
      if (!campo || (regex && !regex.test(campo))) {
        $(`#error${mensaje}`).html(`Por favor, ingrese un ${nombreLegible} válido`).addClass("text-danger");
        isValid = false;
      } else {
        $(`#error${mensaje}`).html("").removeClass("text-danger");
      }
    };

    // Validaciones específicas con nombres legibles
    validarCampo(campos.id_sucursal, "id_sucursal", "Sucursal");
    validarCampo(campos.nombre_usuario, "nombre_usuario", "Nombre de Usuario");
    validarCampo(campos.usuario, "usuario", "Usuario");
    validarCampo(campos.telefono, "TelefonoUsuario", "Teléfono", /^[0-9]{9,15}$/); // Validación de teléfono
    validarCampo(campos.correo, "CorreoUsuario", "Correo", /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/); // Validación de correo
    validarCampo(campos.contrasena, "Contrasena", "Contraseña", /^.{6,}$/); // Contraseña con mínimo 6 caracteres

    // Validación de imagen
    if (!campos.imagen) {
      $("#errorImagenUsuario").html("Por favor, selecciona una imagen").addClass("text-danger");
      isValid = false;
    } else {
      $("#errorImagenUsuario").html("").removeClass("text-danger");
    }

    // Si es válido, enviar formulario
    if (isValid) {
      const datos = new FormData();
      Object.entries(campos).forEach(([key, value]) => {
        datos.append(key, value);
      });

      // Para depuración, mostrar los datos antes de enviarlos
      datos.forEach(element => console.log(element));

      $.ajax({
        url: "ajax/Usuario.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: (respuesta) => {
          const res = JSON.parse(respuesta);
          if (res === "ok") {
            $("#nuevoUsuario")[0].reset();
            $(".vistaPreviaImagenUsuario").attr("src", "");
            $("#modalNuevoUsuario").modal("hide");

            Swal.fire({
              title: "¡Correcto!",
              text: "El usuario ha sido guardado",
              icon: "success",
            });

            mostrarUsuarios();
          } else {
            console.error("La carga y guardado de la imagen ha fallado.");
          }
        },
      });
    }
  });


  /* ===========================
  MOSTRANDO USUARIOS
  =========================== */
  function mostrarUsuarios() {
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (usuarios) {
        var tbody = $("#dataUsuarios");
        tbody.empty();

        usuarios.forEach(function (usuario) {
          usuario.imagen_usuario = usuario.imagen_usuario.substring(3);
          var fila = `
          <tr>
            <td>
              <a href="javascript:void(0);" class="product-img">
                <img src="${usuario.imagen_usuario}" alt="${usuario.nombre_usuario}">
              </a>
            </td>
            <td>${usuario.nombre_sucursal}</td>
            <td>${usuario.nombre_usuario}</td>
            <td>${usuario.usuario}</td>
            <td>${usuario.telefono}</td>
            <td>${usuario.correo}</td>
            <td>
              ${usuario.estado_usuario != 0 ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="0">Activado</button>'
              : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idUsuario="' + usuario.id_usuario + '" estadoUsuario="1">Desactivado</button>'}
            </td>
            <td>
              <a href="#" class="me-3 btnEditarUsuario" idUsuario="${usuario.id_usuario}" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario">
                <i class="text-warning fas fa-edit fa-lg"></i>
              </a>
              <a href="#" class="me-3 btnVerUsuario" idUsuario="${usuario.id_usuario}" data-bs-toggle="modal" data-bs-target="#modalVerUsuario">
                <i class="text-primary fa fa-eye fa-lg"></i>
              </a>
              <a href="#" class="me-3 confirm-text btnEliminarUsuario" idUsuario="${usuario.id_usuario}" fotoUsuario="${usuario.imagen_usuario}">
                <i class="fa fa-trash fa-lg" style="color: #F52E2F"></i>
              </a>
            </td>
          </tr>`;

          tbody.append(fila);
        });

        $('#tabla_usuarios').DataTable()
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
    datos.append("activarId", idUsuario);
    datos.append("activarUsuario", estadoUsuario);
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        console.log(respuesta);
        if (window.matchMedia("(max-width:767px)").matches) {

          swal({
            title: "El usuario ha sido actualizado",
            type: "success",
            confirmButtonText: "¡Cerrar!"
          }).then(function (result) {
            if (result.value) {

              window.location = "usuarios";

            }


          });

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
    datos.append("idUsuario", idUsuario);
    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {

        $("#edit_id_usuario").val(respuesta["id_usuario"]);
        $("#edit_id_sucursal").append(
          '<option value="' +
          respuesta["id_sucursal"] +
          '" selected>' +
          respuesta["nombre_sucursal"] +
          "</option>"
        );
        $("#edit_nombre_usuario").val(respuesta["nombre_usuario"]);
        $("#edit_telefono_usuario").val(respuesta["telefono"]);
        $("#edit_correo_usuario").val(respuesta["correo"]);
        $("#edit_usuario_usuario").val(respuesta["usuario"]);
        $("#edit_password_actual").val(respuesta["contrasena"]);

        var imagenUsuario = respuesta["imagen_usuario"].substring(3);

        if (respuesta["imagen_usuario"] != "") {
          $(".edit_vista_imagen_usuario").attr("src", imagenUsuario);
        } else {
          $(".edit_vista_imagen_usuario").attr(
            "src",
            "vistas/img/usuarios/default.jpeg"
          );
        }

        $("#edit_imagen_actual_usuario").val(respuesta["imagen_usuario"]);

      },
    });
  });

  /*=============================================
  MOSTRAR DETALLE DEL USUARIO
  =============================================*/
  $("#tabla_usuarios").on("click", ".btnVerUsuario", function () {

    var idUsuarioVer = $(this).attr("idUsuario");


    var datos = new FormData();
    datos.append("idUsuarioVer", idUsuarioVer);

    $.ajax({
      url: "ajax/Usuario.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        let EstadoUsuario = '';
        $("#mostrar_nombre_usuario").text(respuesta["nombre_usuario"]);
        $("#mostrar_nombre_sucursal").text(respuesta["nombre_sucursal"]);
        $("#mostrar_telefono_usuario").text(respuesta["telefono"]);
        $("#mostrar_correo_usuario").text(respuesta["correo"]);
        $("#mostrar_usuario").text(respuesta["usuario"]);
        if(respuesta["estado_usuario"] == 1){
          EstadoUsuario = "Usuario activado";
        }else{
          EstadoUsuario = "Usuario desactivado";
        }
        $("#mostrar_estado_usuario").text(EstadoUsuario);
        var imagenUsuario = respuesta["imagen_usuario"].substring(3);

        if (respuesta["imagen_usuario"] != "") {
          $(".mostrarFotoUsuario").attr("src", imagenUsuario);
        } else {
          $(".mostrarFotoUsuario").attr(
            "src",
            "vistas/img/usuarios/default/anonymous.png"
          );
        }
      },
    });
  });

  /*===========================================
  ACTUALIZAR EL USUARIO
  =========================================== */
  $("#btn_update_usuario").click(function (e) {
    e.preventDefault();

    // Variables para los campos
    let edit_idSucursal = $("#edit_id_sucursal").val();
    let edit_nombreUsuario = $("#edit_nombre_usuario").val().trim();
    let edit_telefonoUsuario = $("#edit_telefono_usuario").val().trim();
    let edit_correoUsuario = $("#edit_correo_usuario").val().trim();
    let edit_usuarioUsuario = $("#edit_usuario_usuario").val().trim();
    let edit_contrasena = $("#edit_new_password_usuario").val();
    let edit_actualContrasena = $("#edit_password_actual").val();
    let edit_imagen = $("#edit_new_imagen_usuario")[0].files[0]; // Para obtener el archivo seleccionado
    let edit_imagenActualUsuario = $("#edit_imagen_actual_usuario").val();

    let isValid = true;

    // Limpiar errores previos
    $(".text-danger").text("");

    // Validaciones
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

    // Si todo es válido, enviar la solicitud
    if (isValid) {
      var datos = new FormData();
      datos.append("edit_idUsuario", $("#edit_id_usuario").val());
      datos.append("edit_idSucursal", edit_idSucursal);
      datos.append("edit_nombre", edit_nombreUsuario);
      datos.append("edit_telefono", edit_telefonoUsuario);
      datos.append("edit_correo", edit_correoUsuario);
      datos.append("edit_usuario", edit_usuarioUsuario);
      datos.append("edit_contrasena", edit_contrasena);
      datos.append("edit_actualContrasena", edit_actualContrasena);
      datos.append("edit_imagen", edit_imagen);
      datos.append("edit_imagenActualUsuario", edit_imagenActualUsuario);
      $.ajax({
        url: "ajax/Usuario.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          try {
            var res = JSON.parse(respuesta);
            if (res === "ok") {
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
    var deleteRutaUser = "../" + deletefotoUser;


    var datos = new FormData();
    datos.append("deleteUserId", deleteUserId);
    datos.append("deleteRutaUser", deleteRutaUser);

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
            var res = JSON.parse(respuesta);

            if (res === "ok") {

              Swal.fire({
                title: "¡Eliminado!",
                text: "El usuario ha sido eliminado",
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
  }
  );

  /*   ==========================================
    LIMPIAR MODALES
    ========================================== */

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
        usuarios.forEach(function (usuario, index) {
          usuario.imagen_usuario = usuario.imagen_usuario.substring(3);
          var fila = `
        <tr>
            <td>${index + 1}</td> <!-- Mostrar el índice sumando 1 para comenzar desde 1 -->
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


        // Inicializar DataTables después de cargar los datos

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

    // Esperar a que se cargue la página antes de imprimir
    newWindow.onload = () => {
      newWindow.print();
    };
  });




});
