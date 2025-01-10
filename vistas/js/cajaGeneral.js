$(document).ready(function () {

    /* ===========================================
    GUARDAR CAJA GENRAL APERTURA
    =========================================== */
    $("#btn_guardar_apertura_caja").click(function (e) {
      e.preventDefault();
      let isValid = true;
      let id_usuario_caja = $("#id_usuario_caja").val();
      let monto_inicial_caja = $("#monto_inicial_caja").val();
      let fecha_apertura_caja = $("#fecha_apertura_caja").val();
      let fecha_cierre_caja = $("#fecha_cierre_caja").val();

      // Limpia los errores previos
      $("#error_monto_inicial_caja").text("");
      $("#fecha_apertura_caja").removeClass("is-invalid");
      $("#fecha_cierre_caja").removeClass("is-invalid");

      // Validación del monto inicial
      if (!monto_inicial_caja || parseFloat(monto_inicial_caja) <= 0) {
        $("#error_monto_inicial_caja").text(
          "El monto inicial debe ser un número positivo."
        );
        $("#monto_inicial_caja").addClass("is-invalid");
        isValid = false;
      }

      // Validación de fecha de apertura
      if (!fecha_apertura_caja) {
        $("#fecha_apertura_caja").addClass("is-invalid");
        isValid = false;
      }

      // Validación de fecha de cierre
      if (!fecha_cierre_caja) {
        $("#fecha_cierre_caja").addClass("is-invalid");
        isValid = false;
      }

 
      if (isValid) {
        var datos = new FormData();
        datos.append("id_usuario_caja", id_usuario_caja);
        datos.append("monto_inicial_caja", monto_inicial_caja);
        datos.append("fecha_apertura_caja", fecha_apertura_caja);
        datos.append("fecha_cierre_caja", fecha_cierre_caja);
        $.ajax({
          url: "ajax/Caja.general.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function (respuesta) {
            var res = JSON.parse(respuesta);
            if (res.status === true) {
              $("#form_nuevo_apertura_caja")[0].reset();
              $("#modal_nuevo_apertura_caja").modal("hide");
              Swal.fire({
                title: "¡Correcto!",
                text: res.message,
                icon: "success",
              });
              mostrarCajaGeneral();
            } else {
                Swal.fire({
                    title: "¡Aviso!",
                    text: res.message,
                    icon: "warning",
                  });
            }
          },
        });
      }
    });
  
    /* ===========================
    MOSTRANDO CAJA GENRAL APERTURA
    =========================== */
    function mostrarCajaGeneral() {
      $.ajax({
          url: "ajax/Caja.general.ajax.php",
          type: "GET",
          dataType: "json",
          success: function (respuesta) {
              var tbody = $("#data_list_caja");
              tbody.empty();
              respuesta.forEach(function (item, index) {
                var fila = '';
                if(item.estado === "cerrado"){
                    fila = `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${item.fecha_cierre}</td>
                                <td class="text-center">USD ${item.ingresos}</td>
                                <td class="text-center">USD ${item.egresos}</td>
                                <td class="text-center">USD ${item.monto_inicial}</td>
                                <td class="text-center">USD ${item.monto_final}</td>
                            </tr>
                        `;
                }
                tbody.append(fila);
              });
  
              // Inicializar DataTables después de cargar los datos
              $('#tabla_apertura_cierre_caja').DataTable();
          },
          error: function (xhr, status, error) {
              console.error("Error al recuperar los proveedores:", error);
          },
      });
    }

    /* ===========================
    MOSTRANDO CAJA GENRAL APERTURA
    =========================== */
    function mostrarCajaGeneralApertura() {
      $.ajax({
          url: "ajax/Caja.general.ajax.php",
          type: "GET",
          dataType: "json",
          success: function (respuesta) {
            if (respuesta.length > 0) {
                respuesta.forEach(function (item) {
                    if(item.estado === "abierto"){
                        $("#total_ingresos_caja").text(item.ingresos);
                        $("#total_egresos_caja").text(item.egresos);
                        $("#total_saldo_inicial_caja").text(item.monto_inicial);
                        $("#monto_totol_caja").text(item.monto_final);
                    }
                });
            } else {
                console.log('No hay datos disponibles.');
            }
        }
        
      });
    }

    /*=============================================
    EDITAR CAJA GENRAL APERTURA
    =============================================*/
    $("#tabla_categoria").on("click", ".btnEditarCategoria", function () {

      var idCategoria = $(this).attr("idCategoria");
  
      var datos = new FormData();
      datos.append("idCategoria", idCategoria);
  
      $.ajax({
        url: "ajax/Caja.general.ajax.php",
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
    ACTUALIZAR CAJA GENRAL APERTURA
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
      


      // Validar el descripcion de categoria
      if (edit_descripcion_categoria === "") {
        $("#edit_error_descripcion_categoria")
          .html("Por favor, ingrese la descripción")
          .addClass("text-danger");
        isValid = false;
      } else if (!isNaN(edit_descripcion_categoria)) {
        $("#edit_error_descripcion_categoria")
          .html("La descripción no puede contener números")
          .addClass("text-danger");
        isValid = false;
      } else {
        $("#edit_error_descripcion_categoria").html("").removeClass("text-danger");
      }
      
      

      if (isValid) {
        var datos = new FormData();
        datos.append("edit_id_categoria", edit_id_categoria);
        datos.append("edit_nombre_categoria", edit_nombre_categoria);
        datos.append("edit_descripcion_categoria", edit_descripcion_categoria);


        $.ajax({
          url: "ajax/Caja.general.ajax.php",
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

              mostrarCajaGeneral();

            } else {
              console.error("Error al cargar los datos.");
            }
          }
        });
      }
    });
  
    /*=============================================
      ELIMINAR CAJA GENRAL APERTURA
      =============================================*/
    $("#tabla_categoria").on("click",".btnEliminarCategoria",function (e) {
  
        e.preventDefault();
  
        var deleteIdCategoria = $(this).attr("idCategoria");
  
        var datos = new FormData();
        datos.append("deleteIdCategoria", deleteIdCategoria);
  
        Swal.fire({
          title: "¿Está seguro de borrar la categoría?",
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
              url: "ajax/Caja.general.ajax.php",
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
                    text: "La categoria ha sido eliminado",
                    icon: "success",
                  });
      
                  mostrarCajaGeneral();
  
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
    MOSTRANDO CAJA GENRAL APERTURA
    ===================================== */
    mostrarCajaGeneral();
    mostrarCajaGeneralApertura();

  });



  