$(document).ready(function() {

  // Obtener sesión del usuario
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

  // Establecer fecha y hora actual por defecto
  var currentDate = new Date().toISOString().slice(0, 10);
  var currentTime = new Date().toTimeString().slice(0, 5);
  var dt = new Date();
  dt.setHours(dt.getHours() + 8);
  var futureTime = dt.toTimeString().slice(0, 5);

  $('#fecha_asistencia_a').val(currentDate);
  $('#hora_entrada_a').val(currentTime);
  $('#hora_salida_a').val(futureTime);

  // Mostrar lista de asistencias
  async function mostrarAsistencia() {
      const sesion = await obtenerSesion();
      if(!sesion) return;
      $.ajax({
          url: "ajax/Asistencia.ajax.php",
          type: "GET",
          dataType: "json",
          success: function(asistencias) {
              var tbody = $("#data_mostrar_asistencias");
              tbody.empty();
              let fechasRegistradas = [];
              
              asistencias.forEach(function(asistencia, index) {
                  if (!fechasRegistradas.includes(asistencia.fecha_asistencia)) {
                      fechasRegistradas.push(asistencia.fecha_asistencia);
                      let fila = `
                          <tr>
                              <td>${index + 1}</td>
                              <td>${asistencia.fecha_asistencia}</td>
                              <td class="text-center">
                                  ${sesion.permisos.asistencia && sesion.permisos.asistencia.acciones.includes("editar") ? 
                                      `<a href="#" class="me-3 btnEditarAsistencia" fechaAsistencia="${asistencia.fecha_asistencia}" data-bs-toggle="modal" data-bs-target="#modalEditarAsistencia">
                                          <i class="text-warning fas fa-edit fa-lg"></i>
                                      </a>` : ``}
                                  ${sesion.permisos.asistencia && sesion.permisos.asistencia.acciones.includes("ver") ? 
                                      `<a href="#" class="me-3 btnVerAsistencia" fechaAsistencia="${asistencia.fecha_asistencia}" data-bs-toggle="modal" data-bs-target="#modalVerAsistencia">
                                          <i class="text-primary fa fa-eye fa-lg"></i>
                                      </a>` : ``}
                                  ${sesion.permisos.asistencia && sesion.permisos.asistencia.acciones.includes("eliminar") ? 
                                      `<a href="#" class="me-3 confirm-text btnEliminarAsistencia" fechaAsistencia="${asistencia.fecha_asistencia}">
                                          <i class="fa fa-trash fa-lg" style="color: #F52E2F"></i>
                                      </a>` : ``}
                              </td>
                          </tr>`;
                      tbody.append(fila);
                  }
              });

              $('#tabla_asistencia').DataTable();
          },
          error: function(xhr, status, error) {
              console.error("Error al recuperar las asistencias:", error);
          }
      });
  }

  // Guardar nueva asistencia
  $("#btn_guardar_asistencia").click(function(e) {
      e.preventDefault();

      var isValid = true;
      var fecha_asistencia = $("#fecha_asistencia_a").val();
      var hora_entrada = $("#hora_entrada_a").val();
      var hora_salida = $("#hora_salida_a").val();

      // Validaciones
      if(fecha_asistencia == "") {
          Swal.fire("Error", "Por favor ingrese la fecha", "error");
          return false;
      }

      if(hora_entrada == "") {
          Swal.fire("Error", "Por favor ingrese la hora de entrada", "error");
          return false;
      }

      if(hora_salida == "") {
          Swal.fire("Error", "Por favor ingrese la hora de salida", "error");
          return false;
      }

      // Recolectar datos de los trabajadores
      var datosAsistencia = [];
      $("#show_estado_asistencia tr").each(function() {
          var fila = $(this);
          var idTrabajador = fila.find("#id_trabajador_asistencia").val();
          var estado = fila.find("input[type='radio']:checked").val();
          var observacion = fila.find("input[type='text']").val();

          datosAsistencia.push({
              id_trabajador: idTrabajador,
              estado: estado || "Falta",
              observacion: observacion || ""
          });
      });

      // Enviar datos al servidor
      if(isValid) {
          var datos = new FormData();
          datos.append("fecha_asistencia_a", fecha_asistencia);
          datos.append("hora_entrada_a", hora_entrada);
          datos.append("hora_salida_a", hora_salida);
          datos.append("datosAsistenciaJSON", JSON.stringify(datosAsistencia));

          $.ajax({
              url: "ajax/Asistencia.ajax.php",
              method: "POST",
              data: datos,
              cache: false,
              contentType: false,
              processData: false,
              success: function(respuesta) {
                  var res = JSON.parse(respuesta);
                  if(res === "ok") {
                      $("#form_nuevo_asistencia")[0].reset();
                      $("#modalNuevoAsistencia").modal("hide");
                      Swal.fire({
                          title: "¡Correcto!",
                          text: "La asistencia ha sido guardada",
                          icon: "success"
                      });
                      mostrarAsistencia();
                  } else {
                      Swal.fire("Error", "Hubo un problema al guardar la asistencia", "error");
                  }
              },
              error: function() {
                  Swal.fire("Error", "Error de conexión con el servidor", "error");
              }
          });
      }
  });

  // Editar asistencia - Cargar datos
  $("#tabla_asistencia").on("click", ".btnEditarAsistencia", function(e) {
      e.preventDefault();
      var fechaAsistencia = $(this).attr("fechaAsistencia");
      
      // Limpiar tabla de edición
      $("#edit_show_estado_asistencia").empty();
      
      // Cargar datos generales
      $.ajax({
          url: "ajax/Asistencia.ajax.php",
          method: "POST",
          data: {fechaAsistencia: fechaAsistencia},
          dataType: "json",
          success: function(respuesta) {
              $("#edit_fecha_asistencia_a").val(respuesta.fecha_asistencia);
              $("#edit_hora_entrada_a").val(respuesta.hora_entrada);
              $("#edit_hora_salida_a").val(respuesta.hora_salida);
          }
      });
      
      // Cargar lista de trabajadores
      $.ajax({
          url: "ajax/Asistencia.ajax.php",
          method: "POST",
          data: {fechaAsistenciaVer: fechaAsistencia},
          dataType: "json",
          success: function(detalleAsistencia) {
              let contador = 1;
              detalleAsistencia.forEach((trabajador) => {
                  const estado = trabajador.estado || '';
                  const observacion = trabajador.observaciones || '';
                  
                  const nuevaFila = `
                      <tr>
                          <th scope="row">${contador}</th>
                          <td>
                              ${trabajador.nombre}
                              <input type="hidden" id="edit_id_trabajador_asistencia${contador}" value="${trabajador.id_trabajador}">
                          </td>
                          <td class="text-center">
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="asistencia${contador}" id="edit_presente${contador}" value="Presente" ${estado === 'Presente' ? 'checked' : ''}>
                                  <label class="form-check-label" style="color: #28C76F" for="edit_presente${contador}">Presente</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="asistencia${contador}" id="edit_tarde${contador}" value="Tarde" ${estado === 'Tarde' ? 'checked' : ''}>
                                  <label class="form-check-label" style="color: #FF9F43" for="edit_tarde${contador}">Tarde</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="asistencia${contador}" id="edit_falta${contador}" value="Falta" ${estado === 'Falta' ? 'checked' : ''}>
                                  <label class="form-check-label" style="color: #FF4D4D" for="edit_falta${contador}">Falta</label>
                              </div>
                          </td>
                          <td>
                              <input type="text" class="form-control" id="edit_observacion_asistencia${contador}" value="${observacion}" placeholder="Observación">
                          </td>
                      </tr>`;
                  
                  $("#edit_show_estado_asistencia").append(nuevaFila);
                  contador++;
              });
          }
      });
  });

  // Actualizar asistencia
  $("#btn_actualizar_asistencia").click(function(e) {
      e.preventDefault();

      var isValid = true;
      var fecha_asistencia = $("#edit_fecha_asistencia_a").val();
      var hora_entrada = $("#edit_hora_entrada_a").val();
      var hora_salida = $("#edit_hora_salida_a").val();

      // Validaciones
      if(fecha_asistencia == "") {
          Swal.fire("Error", "Por favor ingrese la fecha", "error");
          return false;
      }

      if(hora_entrada == "") {
          Swal.fire("Error", "Por favor ingrese la hora de entrada", "error");
          return false;
      }

      if(hora_salida == "") {
          Swal.fire("Error", "Por favor ingrese la hora de salida", "error");
          return false;
      }

      // Recolectar datos de los trabajadores
      var datosAsistencia = [];
      $("#edit_show_estado_asistencia tr").each(function() {
          var fila = $(this);
          var idTrabajador = fila.find("input[type='hidden']").val();
          var estado = fila.find("input[type='radio']:checked").val();
          var observacion = fila.find("input[type='text']").val();

          datosAsistencia.push({
              id_trabajador: idTrabajador,
              estado: estado || "Falta",
              observacion: observacion || ""
          });
      });

      // Enviar datos al servidor
      if(isValid) {
          var datos = new FormData();
          datos.append("fecha_asistencia", fecha_asistencia);
          datos.append("hora_entrada", hora_entrada);
          datos.append("hora_salida", hora_salida);
          datos.append("datosAsistencia", JSON.stringify(datosAsistencia));

          $.ajax({
              url: "ajax/Asistencia.ajax.php",
              method: "POST",
              data: datos,
              cache: false,
              contentType: false,
              processData: false,
              success: function(respuesta) {
                  var res = JSON.parse(respuesta);
                  if(res === "ok") {
                      $("#modalEditarAsistencia").modal("hide");
                      Swal.fire({
                          title: "¡Correcto!",
                          text: "La asistencia ha sido actualizada",
                          icon: "success"
                      });
                      mostrarAsistencia();
                  } else {
                      Swal.fire("Error", "Hubo un problema al actualizar la asistencia", "error");
                  }
              },
              error: function() {
                  Swal.fire("Error", "Error de conexión con el servidor", "error");
              }
          });
      }
  });

  // Ver asistencia
  $("#tabla_asistencia").on("click", ".btnVerAsistencia", function(e) {
      e.preventDefault();
      var fechaAsistencia = $(this).attr("fechaAsistencia");
      
      // Limpiar tabla de visualización
      $("#ver_show_estado_asistencia").empty();
      
      // Cargar datos generales
      $.ajax({
          url: "ajax/Asistencia.ajax.php",
          method: "POST",
          data: {fechaAsistencia: fechaAsistencia},
          dataType: "json",
          success: function(respuesta) {
              $("#ver_fecha_asistencia_a").val(respuesta.fecha_asistencia);
              $("#ver_hora_entrada_a").val(respuesta.hora_entrada);
              $("#ver_hora_salida_a").val(respuesta.hora_salida);
          }
      });
      
      // Cargar lista de trabajadores
      $.ajax({
          url: "ajax/Asistencia.ajax.php",
          method: "POST",
          data: {fechaAsistenciaVer: fechaAsistencia},
          dataType: "json",
          success: function(detalleAsistencia) {
              let contador = 1;
              detalleAsistencia.forEach((trabajador) => {
                  const estado = trabajador.estado || '';
                  const observacion = trabajador.observaciones || '';
                  
                  const nuevaFila = `
                      <tr>
                          <th scope="row">${contador}</th>
                          <td>${trabajador.nombre}</td>
                          <td class="text-center">
                              <span class="badge ${estado === 'Presente' ? 'bg-success' : estado === 'Tarde' ? 'bg-warning' : 'bg-danger'}">
                                  ${estado}
                              </span>
                          </td>
                          <td>${observacion}</td>
                      </tr>`;
                  
                  $("#ver_show_estado_asistencia").append(nuevaFila);
                  contador++;
              });
          }
      });
  });

  // Eliminar asistencia
  $("#tabla_asistencia").on("click", ".btnEliminarAsistencia", function(e) {
      e.preventDefault();
      var fechaAsistencia = $(this).attr("fechaAsistencia");
      
      Swal.fire({
          title: "¿Está seguro de eliminar esta asistencia?",
          text: "Esta acción no se puede deshacer",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar"
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "ajax/Asistencia.ajax.php",
                  method: "POST",
                  data: {fechaAsistenciaDelete: fechaAsistencia},
                  dataType: "json",
                  success: function(respuesta) {
                      if(respuesta === "ok") {
                          Swal.fire(
                              "¡Eliminado!",
                              "La asistencia ha sido eliminada.",
                              "success"
                          );
                          mostrarAsistencia();
                      } else {
                          Swal.fire(
                              "Error",
                              "Hubo un problema al eliminar la asistencia",
                              "error"
                          );
                      }
                  }
              });
          }
      });
  });

  // Limpiar modales al cerrar
  $('.close_modal_asistencia').click(function() {
      $('#form_editar_asistencia')[0].reset();
      $('#form_ver_asistencia')[0].reset();
      $('#edit_show_estado_asistencia').empty();
      $('#ver_show_estado_asistencia').empty();
  });

  // Inicializar la tabla de asistencias
  mostrarAsistencia();
});