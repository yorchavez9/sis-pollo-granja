$(document).ready(function () {

  /* ===========================================
  SELECIONAR TODOS LOS CHECKBOX
  =========================================== */
  function selecionarCheckbox() {
    // Evento para el checkbox "Seleccionar todos"
    $('#select_all').on('change', function () {
      // Verificar si el checkbox está seleccionado
      const isChecked = $(this).is(':checked');

      // Seleccionar o deseleccionar todos los checkboxes
      $('input[type="checkbox"]').not('#select_all').prop('checked', isChecked);
    });

    // Evento para desmarcar "Seleccionar todos" si alguno se desmarca
    $('input[type="checkbox"]').not('#select_all').on('change', function () {
      if (!$(this).is(':checked')) {
        $('#select_all').prop('checked', false);
      }
      // Si todos los checkboxes están seleccionados, marcar "Seleccionar todos"
      else if ($('input[type="checkbox"]').not('#select_all').length === $('input[type="checkbox"]:checked').not('#select_all').length) {
        $('#select_all').prop('checked', true);
      }
    });

  }
  selecionarCheckbox();

  /* ===========================================
  GUARDAR PERMISOS
  =========================================== */
  // Validar usuario y rol
  function validarUsuarioYRol(idUsuario, idRol) {
    if (!idUsuario) {
      Swal.fire({
        title: "¡Aviso!",
        text: "Seleccione un usuario",
        icon: "warning",
      });
      return false;
    }
    if (!idRol) {
      Swal.fire({
        title: "¡Aviso!",
        text: "Seleccione un rol",
        icon: "warning",
      });
      return false;
    }
    return true;
  }

  // Validar módulos y acciones
  function validarModulosYAcciones() {
    const modulosAcciones = {};
    let moduloSinAcciones = false;

    $('input[type="checkbox"][id^="id_modulo_"]:checked').each(function () {
      const moduloId = $(this).val();
      const accionesDelModulo = [];
      $(this)
        .closest(".card")
        .find('input[type="checkbox"][id^="accion_"]:checked')
        .each(function () {
          accionesDelModulo.push($(this).val());
        });

      if (accionesDelModulo.length === 0) {
        moduloSinAcciones = true;
        return false; // Salir del bucle
      }

      modulosAcciones[moduloId] = accionesDelModulo;
    });

    if (moduloSinAcciones) {
      Swal.fire({
        title: "¡Aviso!",
        text: "Por favor, seleccione al menos una acción para cada módulo.",
        icon: "warning",
      });
      return null;
    }

    if (Object.keys(modulosAcciones).length === 0) {
      Swal.fire({
        title: "¡Aviso!",
        text: "Por favor, seleccione al menos un módulo con sus acciones.",
        icon: "warning",
      });
      return null;
    }

    return modulosAcciones;
  }

  // Guardar datos
  function guardarDatos(idUsuario, idRol, modulosAcciones) {
    const datos = new FormData();
    datos.append("id_usuario_permiso", idUsuario);
    datos.append("id_rol_permiso", idRol);
    datos.append("modulosAcciones", JSON.stringify(modulosAcciones));

    $.ajax({
      url: "ajax/Usuario.permisos.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (response) {
        const res = JSON.parse(response);
        if (res.status === true) {
          $("#form_rol_modulo_accion")[0].reset();
          $("#modal_nuevo_permisos_usuario").modal("hide");
          Swal.fire({
            title: "¡Correcto!",
            text: res.message,
            icon: "success",
          });
          mostrarUsuarioPermisos();
        } else {
          Swal.fire({
            title: "¡Error!",
            text: res.message,
            icon: "error",
          });
        }
      },
      error: function (error) {
        Swal.fire({
          title: "¡Error!",
          text: "Ocurrió un problema al guardar los datos.",
          icon: "error",
        });
        console.error(error);
      },
    });
  }

  // Evento del botón "Guardar"
  $('#btn_guardar_rol_modulo_accion').on('click', function (e) {
    e.preventDefault();

    const idUsuario = $('#id_usuario_permiso').val();
    const idRol = $('#id_rol_permiso').val();

    // Validar usuario y rol
    if (!validarUsuarioYRol(idUsuario, idRol)) return;

    // Validar módulos y acciones
    const modulosAcciones = validarModulosYAcciones();
    if (!modulosAcciones) return;

    // Guardar datos
    guardarDatos(idUsuario, idRol, modulosAcciones);
  });


  /* ===========================
  MOSTRANDO PERMISOS
  =========================== */
  function mostrarUsuarioPermisos() {
    $.ajax({
      url: "ajax/Usuario.permisos.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
        let tbody = $("#data_usuario_permisos");
        tbody.empty();

        response.forEach(function (data, index) {
          let modulosArray = data.modulos ? data.modulos.split(',') : [];
          let cantidadModulos = modulosArray.length;
          let fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${data.nombre_usuario}</td>
                        <td>${data.nombre_rol}</td>
                        <td>${cantidadModulos}</td>
                        <td class="text-center">
                            
                            <a href="#" class="me-3 confirm-text btnEliminarUsuarioPermiso" idUsuarioPermiso="${data.id_usuario}" idRolPermiso="${data.id_rol}">
                                <i class="text-danger fa fa-trash fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                `;
          tbody.append(fila);
        });
        $('#tabla_usuario_permisos').DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuario y roles:", error);
        console.log(xhr);
        console.log(status);
      },
    });
  }


  /*=============================================
  EDITAR EL PERMISOS
  =============================================*/
  $("#tabla_categoria").on("click", ".btnEditarCategoria", function () {

    var idCategoria = $(this).attr("idCategoria");

    var datos = new FormData();
    datos.append("idCategoria", idCategoria);

    $.ajax({
      url: "ajax/Categoria.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {

        $("#edit_id_categoria").val(respuesta["id_categoria"]);
        $("#edit_nombre_categoria").val(respuesta["nombre_categoria"]);
        $("#edit_descripcion_categoria").val(respuesta["descripcion"]);

      },
    });
  });


  /*===========================================
  ACTUALIZAR PERMISOS
  =========================================== */
  $("#btn_actualizar_categoria").click(function (e) {

    e.preventDefault();


    var isValid = true;

    var edit_id_categoria = $("#edit_id_categoria").val();
    var edit_nombre_categoria = $("#edit_nombre_categoria").val();
    var edit_descripcion_categoria = $("#edit_descripcion_categoria").val();

    // Validar el nombre de categoríua
    if (edit_nombre_categoria === "") {
      $("#edit_error_nombre_categoria")
        .html("Por favor, ingrese el nombre")
        .addClass("text-danger");
      isValid = false;
    } else if (!isNaN(edit_nombre_categoria)) {
      $("#edit_error_nombre_categoria")
        .html("El nombre no puede contener números")
        .addClass("text-danger");
      isValid = false;
    } else {
      $("#edit_error_nombre_categoria").html("").removeClass("text-danger");
    }




    if (isValid) {
      var datos = new FormData();
      datos.append("edit_id_categoria", edit_id_categoria);
      datos.append("edit_nombre_categoria", edit_nombre_categoria);
      datos.append("edit_descripcion_categoria", edit_descripcion_categoria);


      $.ajax({
        url: "ajax/Categoria.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          var res = JSON.parse(respuesta);

          if (res === "ok") {
            $("#form_actualizar_categoria")[0].reset();

            $("#modalEditarCategoria").modal("hide");

            Swal.fire({
              title: "¡Correcto!",
              text: "La categoría ha sido actualizado",
              icon: "success",
            });

            mostrarUsuarioPermisos();

          } else {
            console.error("Error al cargar los datos.");
          }
        }
      });
    }
  });

  /*=============================================
    ELIMINAR PERMISOS
    =============================================*/
  $("#tabla_usuario_permisos").on("click", ".btnEliminarUsuarioPermiso", function (e) {
    e.preventDefault();

    let idUsuarioPermisoDelete = $(this).attr("idUsuarioPermiso");
    let idRolPermisoDelete = $(this).attr("idRolPermiso");

    const datos = new FormData();
    datos.append("idUsuarioPermisoDelete", idUsuarioPermisoDelete);
    datos.append("idRolPermisoDelete", idRolPermisoDelete);

    // Confirmación para eliminar
    Swal.fire({
      title: "¿Está seguro de borrar el permiso?",
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
          url: "ajax/Usuario.permisos.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function (respuesta) {
            var res = JSON.parse(respuesta);
            if (res.status === true) {

              Swal.fire({
                title: "¡Eliminado!",
                text: res.message,
                icon: "success",
              });

              mostrarUsuarioPermisos();

            } else {

              Swal.fire({
                title: "¡Error!",
                text: res.message,
                icon: "error",
              });

            }
          }
        });

      }
    });
  }
  );

  /* =====================================
  MOSTRANDO PERMISOS
  ===================================== */
  mostrarUsuarioPermisos();

});



