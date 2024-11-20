$(document).ready(function () {

    /* ===========================================
    GUARDAR SUCURSAL
    =========================================== */
    $("#btn_guardar_sucursal").click(function (e) {
        e.preventDefault();
        let nombre_sucursal = $("#nombre_sucursal").val();
        let direccion = $("#direccion_sucursal").val();
        let telefono = $("#telefono_sucursal").val();

        let isValid = true; // Asegúrate de inicializar esta variable

        // Validar el nombre de la sucursal
        if (nombre_sucursal === "") {
            $("#error_nombre_sucursal")
                .html("Por favor, ingrese el nombre")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s,.-]+$/.test(nombre_sucursal)) { // Solo letras y espacios
            $("#error_nombre_sucursal")
                .html("El nombre no puede contener números ni caracteres especiales")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_nombre_sucursal").html("").removeClass("text-danger");
        }

        // Validar la dirección de la sucursal
        if (direccion === "") {
            $("#error_direccion_sucursal")
                .html("Por favor, ingrese la dirección")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s,.\-#]+$/.test(direccion)) { // Permitir números y algunos caracteres comunes
            $("#error_direccion_sucursal")
                .html("La dirección no puede contener caracteres especiales no permitidos")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_direccion_sucursal").html("").removeClass("text-danger");
        }

        // Validar el teléfono de la sucursal
        if (telefono === "") {
            $("#error_telefono_sucursal")
                .html("Por favor, ingrese el teléfono")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^\d{9,12}$/.test(telefono)) { // Solo números, longitud de 9 caracteres
            $("#error_telefono_sucursal")
                .html("El teléfono debe ser un número válido de 12 dígitos")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_telefono_sucursal").html("").removeClass("text-danger");
        }

        // Si el formulario es válido, envíalo
        if (isValid) {
            var datos = new FormData();
            datos.append("nombre_sucursal", nombre_sucursal);
            datos.append("direccion", direccion);
            datos.append("telefono", telefono);
            $.ajax({
                url: "ajax/Sucursal.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    var res = JSON.parse(respuesta);
                    if (res === "ok") {
                        $("#form_nuevo_sucursal")[0].reset();
                        $("#modal_nuevo_sucursal").modal("hide");
                        Swal.fire({
                            title: "¡Correcto!",
                            text: "Los datos guardados con éxito",
                            icon: "success",
                        });
                        mostrarSucursal();
                    } else {
                        console.error("Error al cargar los datos.");
                    }
                },
            });
        }
    });

    /* ===========================
    MOSTRANDO CATEGORIA
    =========================== */
    function mostrarSucursal() {
        $.ajax({
            url: "ajax/Sucursal.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (sucursales) {
                var tbody = $("#data_sucursal");
                tbody.empty();
                sucursales.forEach(function (sucursal, index) {
                    var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${sucursal.nombre_sucursal}</td>
                        <td>${sucursal.direccion}</td>
                        <td>${sucursal.telefono}</td>
                        <td>
                            ${sucursal.estado != 0 ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idSucursal="' + sucursal.id_sucursal + '" estadoSucursal="0">Activado</button>'
                            : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idSucursal="' + sucursal.id_sucursal + '" estadoSucursal="1">Desactivado</button>'
                            }
                        </td>
                        <td class="text-center">
                            <a href="#" class="me-3 btnEditarSucursal" idSucursal="${sucursal.id_sucursal}" data-bs-toggle="modal" data-bs-target="#modal_editar_sucursal">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>
                            <a href="#" class="me-3 confirm-text btnEliminarSucursal" idSucursal="${sucursal.id_sucursal}">
                                <i class="text-danger fa fa-trash fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                `;
                    tbody.append(fila);
                });

                // Inicializar DataTables después de cargar los datos
                $('#tabla_categoria').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los proveedores:", error);
            },
        });
    }

    /*=============================================
    ACTIVAR SUCURSAL
    =============================================*/
    $("#tabla_sucursal").on("click", ".btnActivar", function () {
        var idSucursal = $(this).attr("idSucursal");
        var estadoSucursal = $(this).attr("estadoSucursal");
        var datos = new FormData();
        datos.append("activarId", idSucursal);
        datos.append("activarSucursal", estadoSucursal);
        $.ajax({
            url: "ajax/Sucursal.ajax.php",
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
                        confirmButtonText: "¡Cerrar!"
                    }).then(function (result) {
                        if (result.value) {
                            window.location = "usuarios";

                        }
                    });
                }
            }
        })

        if (estadoSucursal == 0) {
            $(this)
                .removeClass("bg-lightgreen")
                .addClass("bg-lightred")
                .html("Desactivado");
            $(this).attr("estadoSucursal", 1);
        } else {
            $(this)
                .removeClass("bg-lightred")
                .addClass("bg-lightgreen")
                .html("Activado");
            $(this).attr("estadoSucursal", 0);
        }

    })


    /*=============================================
    EDITAR EL SUCURSAL
    =============================================*/
    $("#tabla_sucursal").on("click", ".btnEditarSucursal", function () {
        let idSucursal = $(this).attr("idSucursal");
        let datos = new FormData();
        datos.append("idSucursal", idSucursal);
        $.ajax({
            url: "ajax/Sucursal.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {

                $("#edit_id_sucursal").val(respuesta["id_sucursal"]);
                $("#edit_nombre_sucursal").val(respuesta["nombre_sucursal"]);
                $("#edit_direccion_sucursal").val(respuesta["direccion"]);
                $("#edit_telefono_sucursal").val(respuesta["telefono"]);
            },
        });
    });


    /*===========================================
    ACTUALIZAR CATEGORIA
    =========================================== */
    $("#btn_update_sucursal").click(function (e) {
        e.preventDefault();
        let isValid = true;
        let edit_id_sucursal = $("#edit_id_sucursal").val();
        let nombre_sucursal = $("#edit_nombre_sucursal").val();
        let direccion = $("#edit_direccion_sucursal").val();
        let telefono = $("#edit_telefono_sucursal").val();
        
        // Validar el nombre de la sucursal
        if (nombre_sucursal === "") {
            $("#error_edit_nombre_sucursal")
                .html("Por favor, ingrese el nombre")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s,.-]+$/.test(nombre_sucursal)) { // Solo letras y espacios
            $("#error_edit_nombre_sucursal")
                .html("El nombre no puede contener números ni caracteres especiales")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_edit_nombre_sucursal").html("").removeClass("text-danger");
        }

        // Validar la dirección de la sucursal
        if (direccion === "") {
            $("#error_edit_direccion_sucursal")
                .html("Por favor, ingrese la dirección")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s,.\-#]+$/.test(direccion)) { // Permitir números y algunos caracteres comunes
            $("#error_edit_direccion_sucursal")
                .html("La dirección no puede contener caracteres especiales no permitidos")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_edit_direccion_sucursal").html("").removeClass("text-danger");
        }

        // Validar el teléfono de la sucursal
        if (telefono === "") {
            $("#error_edit_telefono_sucursal")
                .html("Por favor, ingrese el teléfono")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^\d{9,12}$/.test(telefono)) { // Solo números, longitud de 9 caracteres
            $("#error_edit_telefono_sucursal")
                .html("El teléfono debe ser un número válido de 12 dígitos")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_edit_telefono_sucursal").html("").removeClass("text-danger");
        }


        if (isValid) {
            var datos = new FormData();
            datos.append("edit_id_sucursal", edit_id_sucursal);
            datos.append("nombre_sucursal", nombre_sucursal);
            datos.append("direccion", direccion);
            datos.append("telefono", telefono);
            $.ajax({
                url: "ajax/Sucursal.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    let res = JSON.parse(respuesta);
                    if (res === "ok") {
                        $("#form_update_sucursal")[0].reset();
                        $("#modal_editar_sucursal").modal("hide");
                        Swal.fire({
                            title: "¡Correcto!",
                            text: "Datos actualizados con éxito",
                            icon: "success",
                        });
                        mostrarSucursal();
                    } else {
                        console.error("Error al cargar los datos.");
                    }
                }
            });
        }
    });

    /*=============================================
      ELIMINAR SUCURSAL
      =============================================*/
    $("#tabla_sucursal").on("click", ".btnEliminarSucursal", function (e) {
        e.preventDefault();
        var delete_id_sucursal = $(this).attr("idSucursal");
        var datos = new FormData();
        datos.append("delete_id_sucursal", delete_id_sucursal);
        Swal.fire({
            title: "¿Está seguro de borrar la sucursal?",
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
                    url: "ajax/Sucursal.ajax.php",
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
                                text: "Se eliminó con éxito",
                                icon: "success",
                            });
                            mostrarSucursal();
                        } else {
                            console.error("Error al eliminar los datos");
                        }
                    }
                });
            }
        });
    });

    /* =====================================
    MOSTRANDO DATOS
    ===================================== */
    mostrarSucursal();

});
