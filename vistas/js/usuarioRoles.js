$(document).ready(function () {


  /* ===========================================
  SELECIONAR TODOS LOS CHECKBOX
  =========================================== */
  function selecionarCheckbox() {
    // Evento para el checkbox "Seleccionar todos"
    $('#select_all').on('change', function () {
      const isChecked = $(this).is(':checked');
      $('input[type="checkbox"]').not('#select_all').prop('checked', isChecked);
    });

    $('input[type="checkbox"]').not('#select_all').on('change', function () {
      if (!$(this).is(':checked')) {
        $('#select_all').prop('checked', false);
      }
      else if ($('input[type="checkbox"]').not('#select_all').length === $('input[type="checkbox"]:checked').not('#select_all').length) {
        $('#select_all').prop('checked', true);
      }
    });
  }
  selecionarCheckbox();

  /* ===========================================
  GUARDAR PERMISOS
  =========================================== */
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
        return false;
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

  function actualizarPermisos(idUsuario, idRol, modulosAcciones) {
    const datos = new FormData();
    datos.append("edit_id_usuario", idUsuario);
    datos.append("edit_id_rol", idRol);
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
          $("#edit_mode").remove();
          $("#edit_id_usuario").remove();
          $("#edit_id_rol").remove();
          $("#modal_nuevo_permisos_usuario .modal-title").text("Crear permisos para el usuario");
          $("#btn_guardar_rol_modulo_accion").html('<i class="fa fa-save"></i> Guardar');
          $("#id_usuario_permiso").prop("disabled", false);

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
          text: "Ocurrió un problema al actualizar los datos.",
          icon: "error",
        });
        console.error(error);
      },
    });
  }

  $('#btn_guardar_rol_modulo_accion').on('click', function (e) {
    e.preventDefault();

    const idUsuario = $('#id_usuario_permiso').val();
    const idRol = $('#id_rol_permiso').val();
    const isEditMode = $('#edit_mode').length > 0;

    if (!validarUsuarioYRol(idUsuario, idRol)) return;

    const modulosAcciones = validarModulosYAcciones();
    if (!modulosAcciones) return;

    if (isEditMode) {
      actualizarPermisos(idUsuario, idRol, modulosAcciones);
    } else {
      guardarDatos(idUsuario, idRol, modulosAcciones);
    }
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
                            <a href="#" class="me-3 btnVerUsuarioPermiso" data-bs-toggle="modal" data-bs-target="#modal_ver_usuario_permisos" 
                               idUsuarioPermiso="${data.id_usuario}" idRolPermiso="${data.id_rol}">
                                <i class="text-info fa fa-eye fa-lg"></i>
                            </a>
                            <a href="#" class="me-3 btnEditarUsuarioPermiso" data-bs-toggle="modal" data-bs-target="#modal_nuevo_permisos_usuario" 
                               idUsuarioPermiso="${data.id_usuario}" idRolPermiso="${data.id_rol}">
                                <i class="text-primary fa fa-edit fa-lg"></i>
                            </a>
                            <a href="#" class="me-3 confirm-text btnEliminarUsuarioPermiso" 
                               idUsuarioPermiso="${data.id_usuario}" idRolPermiso="${data.id_rol}">
                                <i class="text-danger fa fa-trash fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                `;
          tbody.append(fila);
        });

        if ($.fn.DataTable.isDataTable('#tabla_usuario_permisos')) {
          $('#tabla_usuario_permisos').DataTable().destroy();
        }
        $('#tabla_usuario_permisos').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
          }
        });
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuario y roles:", error);
      },
    });
  }

  /*=============================================
  EDITAR PERMISOS
  =============================================*/
  $("#tabla_usuario_permisos").on("click", ".btnEditarUsuarioPermiso", function (e) {
    e.preventDefault();

    let idUsuario = $(this).attr("idUsuarioPermiso");
    let idRol = $(this).attr("idRolPermiso");

    $("#modal_nuevo_permisos_usuario .modal-title").text("Editar permisos del usuario");
    $("#btn_guardar_rol_modulo_accion").html('<i class="fa fa-save"></i> Actualizar');

    $.ajax({
      url: "ajax/Usuario.permisos.ajax.php",
      method: "POST",
      data: { idCategoria: idUsuario },
      dataType: "json",
      success: function (response) {
        // Llenar usuario y rol
        $("#id_usuario_permiso").val(response.id_usuario).trigger("change");
        $("#id_rol_permiso").val(response.id_rol).trigger("change");
        $("#id_usuario_permiso").prop("disabled", true);

        // Desmarcar todos los checkboxes primero
        $('#modal_nuevo_permisos_usuario input[type="checkbox"]').prop('checked', false);

        // Marcar los módulos seleccionados
        if (response.ids_modulos) {
          let idsModulos = response.ids_modulos.split(',');
          idsModulos.forEach(function (idModulo) {
            $(`#id_modulo_${idModulo}`).prop('checked', true);
          });
        }

        // Marcar las acciones seleccionadas
        if (response.acciones) {
          let accionesArray = response.acciones.split(',');

          accionesArray.forEach(function (accion) {
            let [idModulo, idAccion] = accion.split(':');

            // Buscar el checkbox de acción dentro del módulo correspondiente
            $(`#id_modulo_${idModulo}`)
              .closest('.card')
              .find(`input[value="${idAccion}"]`)
              .prop('checked', true);
          });
        }

        // Agregar campos ocultos para edición
        if (!$("#edit_mode").length) {
          $("<input>").attr({
            type: "hidden",
            id: "edit_mode",
            name: "edit_mode",
            value: "true"
          }).appendTo("#form_rol_modulo_accion");
        }

        if (!$("#edit_id_usuario").length) {
          $("<input>").attr({
            type: "hidden",
            id: "edit_id_usuario",
            name: "edit_id_usuario",
            value: idUsuario
          }).appendTo("#form_rol_modulo_accion");
        }

        if (!$("#edit_id_rol").length) {
          $("<input>").attr({
            type: "hidden",
            id: "edit_id_rol",
            name: "edit_id_rol",
            value: idRol
          }).appendTo("#form_rol_modulo_accion");
        }
      },
      error: function (error) {
        console.error(error);
        Swal.fire({
          title: "¡Error!",
          text: "No se pudieron cargar los permisos del usuario para editar.",
          icon: "error",
        });
      }
    });
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
  });

  // Limpiar el modal cuando se cierre
  $('#modal_nuevo_permisos_usuario').on('hidden.bs.modal', function () {
    $("#form_rol_modulo_accion")[0].reset();
    $("#edit_mode").remove();
    $("#edit_id_usuario").remove();
    $("#edit_id_rol").remove();
    $("#modal_nuevo_permisos_usuario .modal-title").text("Crear permisos para el usuario");
    $("#btn_guardar_rol_modulo_accion").html('<i class="fa fa-save"></i> Guardar');
    $("#id_usuario_permiso").prop("disabled", false);
  });

  /* =====================================
  MOSTRANDO PERMISOS
  ===================================== */
  mostrarUsuarioPermisos();
});