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


    // Variables globales
    let rolesDisponibles = [];
    let usuariosDisponibles = [];
    let modulosDisponibles = [];
    let accionesDisponibles = [];
    let permisosActuales = [];

    // Configuración común para Select2
    const select2Config = {
        placeholder: "Seleccionar",
    };

    // Inicializar Select2
    function initSelect2(selector, dropdownParent = null) {
        const config = { ...select2Config };
        if (dropdownParent) {
            config.dropdownParent = dropdownParent;
        }
        $(selector).select2(config);
    }

    // Inicializar todos los Select2 al cargar
    initSelect2('.js-example-basic-single');

    // Reinicializar Select2 en modales
    $('#modal_nuevo_permiso, #modal_editar_permiso, #modal_ver_permiso').on('shown.bs.modal', function () {
        initSelect2($(this).find('.js-example-basic-single'), $(this));
    });

    // Función para cargar datos iniciales
    const cargarDatosIniciales = async () => {
        try {
            // Cargar usuarios
            const usuariosResponse = await fetchData("ajax/usuario.ajax.php");

            if (usuariosResponse?.status) {
                usuariosDisponibles = usuariosResponse.data;
                llenarSelectUsuarios("#id_usuario_permiso");
                llenarSelectUsuarios("#edit_id_usuario_permiso");
            }

            // Cargar roles
            const rolesResponse = await fetchData("ajax/rol.ajax.php");
            if (rolesResponse?.status) {
                rolesDisponibles = rolesResponse.data;
                llenarSelectRoles("#id_rol_permiso");
                llenarSelectRoles("#edit_rol_permiso");
            }

            // Cargar módulos
            const modulosResponse = await fetchData("ajax/modulo.ajax.php?estado=1");
            if (modulosResponse?.status) {
                modulosDisponibles = modulosResponse.data;
            }

            // Cargar acciones
            const accionesResponse = await fetchData("ajax/accion.ajax.php");
            if (accionesResponse?.status) {
                accionesDisponibles = accionesResponse.data;
            }

            // Mostrar permisos
            await mostrarPermisos();

            // Inicializar eventos de checkboxes
            inicializarEventosCheckboxes();
        } catch (error) {
            console.error("Error al cargar datos iniciales:", error);
            Swal.fire("Error", "No se pudieron cargar los datos iniciales", "error");
        }
    };

    // Llenar select de usuarios
    const llenarSelectUsuarios = (selector) => {
        const select = $(selector);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar rol</option>');

        usuariosDisponibles.forEach(rol => {
            select.append(`<option value="${rol.id_usuario}">${rol.nombre}</option>`);
        });
    };

    // Llenar select de roles
    const llenarSelectRoles = (selector) => {
        const select = $(selector);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar rol</option>');

        rolesDisponibles.forEach(rol => {
            select.append(`<option value="${rol.id_rol}">${rol.nombre}</option>`);
        });
    };

    // Generar checkboxes de módulos y acciones
    const generarModulosAcciones = (contenedor, permisosRol = []) => {
        const $contenedor = $(contenedor);
        $contenedor.empty();

        // Ordenar módulos alfabéticamente
        const modulosOrdenados = [...modulosDisponibles].sort((a, b) => a.nombre.localeCompare(b.nombre));

        modulosOrdenados.forEach(modulo => {
            // Verificar si el módulo tiene permisos para este rol
            const moduloTienePermisos = permisosRol.some(p => p.id_modulo == modulo.id_modulo);

            // Obtener acciones permitidas para este módulo y rol
            const accionesPermitidas = permisosRol
                .filter(p => p.id_modulo == modulo.id_modulo)
                .map(p => p.id_accion.toString());

            // Crear card para el módulo
            const card = $(`
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="form-check">
                                <input class="form-check-input checkbox-modulo" 
                                       type="checkbox" 
                                       id="modulo_${modulo.id_modulo}" 
                                       value="${modulo.id_modulo}"
                                       ${moduloTienePermisos ? 'checked' : ''}>
                                <label class="form-check-label fw-bold" for="modulo_${modulo.id_modulo}">
                                    ${modulo.nombre}
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input checkbox-select-all" 
                                       type="checkbox" 
                                       id="select_all_${modulo.id_modulo}">
                                <label class="form-check-label" for="select_all_${modulo.id_modulo}">
                                    Seleccionar todas
                                </label>
                            </div>
                            <div class="row" id="acciones_modulo_${modulo.id_modulo}">
                                <!-- Acciones dinámicas -->
                            </div>
                        </div>
                    </div>
                </div>
            `);

            $contenedor.append(card);

            // Generar checkboxes de acciones para este módulo
            generarAccionesModulo(modulo.id_modulo, accionesPermitidas);
        });
    };

    // Generar checkboxes de acciones para un módulo específico
    const generarAccionesModulo = (idModulo, accionesSeleccionadas = []) => {
        const $contenedor = $(`#acciones_modulo_${idModulo}`);
        $contenedor.empty();

        // Filtrar acciones para este módulo (asumiendo que hay una relación)
        // Si no hay relación, mostrar todas las acciones
        const accionesModulo = accionesDisponibles.filter(accion =>
            accion.id_modulo == idModulo || !accion.id_modulo
        ).sort((a, b) => a.nombre.localeCompare(b.nombre));

        accionesModulo.forEach(accion => {
            $contenedor.append(`
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input checkbox-accion" 
                               type="checkbox" 
                               id="accion_${idModulo}_${accion.id_accion}" 
                               value="${accion.id_accion}" 
                               data-modulo="${idModulo}"
                               ${accionesSeleccionadas.includes(accion.id_accion.toString()) ? 'checked' : ''}>
                        <label class="form-check-label" for="accion_${idModulo}_${accion.id_accion}">
                            ${accion.nombre}
                        </label>
                    </div>
                </div>
            `);
        });
    };

    // Inicializar eventos de checkboxes
    const inicializarEventosCheckboxes = () => {
        // Seleccionar/deseleccionar todos los módulos
        $('#select_all_modulos, #edit_select_all_modulos').change(function () {
            const isChecked = $(this).is(':checked');
            const contenedor = $(this).is('#select_all_modulos') ?
                '#contenedor_modulos_acciones' : '#edit_contenedor_modulos_acciones';

            $(`${contenedor} .checkbox-modulo`).prop('checked', isChecked).trigger('change');
        });

        // Seleccionar/deseleccionar todas las acciones de un módulo
        $(document).on('change', '.checkbox-select-all', function () {
            const card = $(this).closest('.card-body');
            const isChecked = $(this).is(':checked');

            card.find('.checkbox-accion').prop('checked', isChecked);

            // Actualizar estado del checkbox del módulo
            const moduloCheckbox = $(this).closest('.card').find('.checkbox-modulo');
            moduloCheckbox.prop('checked', isChecked);
        });

        // Cuando se marca/desmarca un módulo
        $(document).on('change', '.checkbox-modulo', function () {
            const idModulo = $(this).val();
            const isChecked = $(this).is(':checked');

            // Marcar/desmarcar todas las acciones del módulo
            $(`#acciones_modulo_${idModulo} .checkbox-accion`).prop('checked', isChecked);

            // Verificar estado de "Seleccionar todos"
            verificarEstadoSelectAll();
        });

        // Cuando se marca/desmarca una acción
        $(document).on('change', '.checkbox-accion', function () {
            const idModulo = $(this).data('modulo');
            const moduloCheckbox = $(`#modulo_${idModulo}`);

            // Verificar si todas las acciones están seleccionadas
            const todasAcciones = $(`#acciones_modulo_${idModulo} .checkbox-accion`).length;
            const accionesSeleccionadas = $(`#acciones_modulo_${idModulo} .checkbox-accion:checked`).length;

            moduloCheckbox.prop('checked', accionesSeleccionadas > 0);

            // Actualizar estado de "Seleccionar todas" para este módulo
            const selectAllCheckbox = $(this).closest('.card-body').find('.checkbox-select-all');
            selectAllCheckbox.prop('checked', todasAcciones === accionesSeleccionadas);

            // Verificar estado de "Seleccionar todos"
            verificarEstadoSelectAll();
        });
    };

    // Verificar estado del checkbox "Seleccionar todos"
    const verificarEstadoSelectAll = () => {
        const contenedor = $('#contenedor_modulos_acciones').is(':visible') ?
            '#contenedor_modulos_acciones' : '#edit_contenedor_modulos_acciones';
        const selectAllCheckbox = $(contenedor).is('#contenedor_modulos_acciones') ?
            '#select_all_modulos' : '#edit_select_all_modulos';

        const totalModulos = $(`${contenedor} .checkbox-modulo`).length;
        const modulosSeleccionados = $(`${contenedor} .checkbox-modulo:checked`).length;

        $(selectAllCheckbox).prop('checked', totalModulos === modulosSeleccionados);
    };

    // Mostrar permisos en la tabla
    const mostrarPermisos = async () => {
        try {
            const [sesion, response] = await Promise.all([
                obtenerSesion(),
                fetchData("ajax/permiso.ajax.php")
            ]);
            if (!sesion || !sesion.permisos) {
                return;
            }

            if (!response?.status) return;

            permisosActuales = response.data;
            const tabla = $("#tabla_permisos");
            const tbody = tabla.find("tbody");
            tbody.empty();

            // Agrupar permisos por rol
            const permisosPorRol = permisosActuales.reduce((acc, permiso) => {
                if (!acc[permiso.id_rol]) {
                    acc[permiso.id_rol] = {
                        rol: rolesDisponibles.find(r => r.id_rol == permiso.id_rol) || { nombre: 'Desconocido' },
                        modulos: new Set(),
                        fecha: permiso.fecha_asignacion,
                        // Buscar el usuario asociado a este rol
                        usuario: response.usuarios_roles.find(u => u.id_rol == permiso.id_rol) || {}
                    };
                }
                acc[permiso.id_rol].modulos.add(permiso.id_modulo);
                return acc;
            }, {});

            // Mostrar en la tabla
            Object.values(permisosPorRol).forEach((permisoRol, index) => {
                const totalModulos = permisoRol.modulos.size || 0;

                const fila = `
                    <tr data-id-rol="${permisoRol.rol.id_rol}" data-id-usuario="${permisoRol.usuario.id_usuario || ''}">
                        <td>${index + 1}</td>
                        <td>${permisoRol.usuario.nombre_usuario || 'No asignado'}</td>
                        <td>${permisoRol.rol.nombre}</td>
                        <td>${totalModulos} módulo(s)</td>
                        <td>${permisoRol.fecha || 'No registrada'}</td>
                        <td class="text-center">
                            ${sesion.permisos.permisos && sesion.permisos.permisos.acciones.includes("ver")?
                                `<a href="#" class="me-3 btnVerPermiso" 
                                data-id-rol="${permisoRol.rol.id_rol}"
                                data-id-usuario="${permisoRol.usuario.id_usuario || ''}"
                                data-bs-toggle="modal" 
                                data-bs-target="#modal_ver_permiso">
                                    <i class="text-primary fas fa-eye fa-lg"></i>
                                </a>`:``}
                            

                            ${sesion.permisos.permisos && sesion.permisos.permisos.acciones.includes("editar")?
                                `<a href="#" class="me-3 btnEditarPermiso" 
                                data-id-rol="${permisoRol.rol.id_rol}"
                                data-id-usuario="${permisoRol.usuario.id_usuario || ''}"
                                data-bs-toggle="modal" 
                                data-bs-target="#modal_editar_permiso">
                                    <i class="text-warning fas fa-edit fa-lg"></i>
                                </a>`:``}
                            

                            ${sesion.permisos.permisos && sesion.permisos.permisos.acciones.includes("eliminar")?
                                `<a href="#" class="me-3 btnEliminarPermiso" 
                                data-id-rol="${permisoRol.rol.id_rol}"
                                data-id-usuario="${permisoRol.usuario.id_usuario || ''}">
                                    <i class="text-danger fa fa-trash fa-lg"></i>
                                </a>`:``}
                            
                        </td>
                    </tr>`;
                tbody.append(fila);
            });


            // Inicializar DataTable si no está inicializado
            if ($.fn.DataTable.isDataTable(tabla)) {
                tabla.DataTable().destroy();
            }
            tabla.DataTable({
                autoWidth: false,
                responsive: true,
            });
        } catch (error) {
            console.error("Error al mostrar permisos:", error);
        }
    };

    // Validar formulario de permiso
    const validarFormularioPermiso = (esEdicion = false) => {
        let valido = true;

        if (!esEdicion) {
            // Validar rol
            if (!$("#id_rol_permiso").val()) {
                $("#error_rol_permiso").text("Debe seleccionar un rol").addClass("text-danger");
                valido = false;
            } else {
                $("#error_rol_permiso").text("").removeClass("text-danger");
            }
            if (!$("#id_usuario_permiso").val()) {
                $("#error_usuario_permiso").text("Debe seleccionar un usuario").addClass("text-danger");
                valido = false;
            } else {
                $("#error_usuario_permiso").text("").removeClass("text-danger");
            }
        }

        // Validar módulos y acciones (al menos un módulo con al menos una acción seleccionada)
        const contenedor = esEdicion ? '#edit_contenedor_modulos_acciones' : '#contenedor_modulos_acciones';
        const modulosSeleccionados = $(`${contenedor} .checkbox-modulo:checked`).length;
        const accionesSeleccionadas = $(`${contenedor} .checkbox-accion:checked`).length;

        if (modulosSeleccionados === 0 || accionesSeleccionadas === 0) {
            $(esEdicion ? "#error_edit_modulos_acciones" : "#error_modulos_acciones")
                .text("Debe seleccionar al menos un módulo con sus acciones").addClass("text-danger");
            valido = false;
        } else {
            $(esEdicion ? "#error_edit_modulos_acciones" : "#error_modulos_acciones")
                .text("").removeClass("text-danger");
        }

        return valido;
    };

    // Obtener permisos seleccionados
    const obtenerPermisosSeleccionados = (contenedor) => {
        const permisos = [];

        $(`${contenedor} .checkbox-modulo:checked`).each(function () {
            const idModulo = $(this).val();

            $(`#acciones_modulo_${idModulo} .checkbox-accion:checked`).each(function () {
                permisos.push({
                    id_modulo: idModulo,
                    id_accion: $(this).val()
                });
            });
        });

        return permisos;
    };

    // Función para hacer fetch
    const fetchData = async (url, method = "GET", data = null) => {
        try {
            const options = {
                method,
                body: data,
                cache: "no-cache",
                headers: data ? {} : { "Content-Type": "application/json" },
            };
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error("Error en la solicitud:", error);
            return null;
        }
    };

    // Evento para guardar nuevo permiso
    $("#btn_guardar_permiso").click(async function (e) {
        e.preventDefault();

        if (!validarFormularioPermiso()) return;

        try {
            const idUsuario = $("#id_usuario_permiso").val();
            const idRol = $("#id_rol_permiso").val();
            const permisos = obtenerPermisosSeleccionados('#contenedor_modulos_acciones');

            const formData = new FormData();
            formData.append('id_usuario', idUsuario);
            formData.append('id_rol', idRol);
            formData.append('permisos', JSON.stringify(permisos));
            formData.append('action', 'guardar');

            const response = await fetchData("ajax/permiso.ajax.php", "POST", formData);

            if (response?.status) {
                Swal.fire("¡Correcto!", response.message || "Permisos guardados correctamente", "success");
                $("#modal_nuevo_permiso").modal("hide");
                $("#form_nuevo_permiso")[0].reset();
                if ($.fn.DataTable.isDataTable("#tabla_permisos")) {
                    $("#tabla_permisos").DataTable().destroy();
                }
                await mostrarPermisos();
            } else {
                Swal.fire("Error", response?.message || "Error al guardar los permisos", "error");
            }
        } catch (error) {
            console.error("Error al guardar permiso:", error);
            Swal.fire("Error", "Ocurrió un error al guardar el permiso", "error");
        }
    });

    // Evento para editar permiso
    $("#tabla_permisos").on("click", ".btnEditarPermiso", async function (e) {
        e.preventDefault();

        const idUsuario = $(this).data('id-usuario');
        const idRol = $(this).data('id-rol');

        try {
            // Buscar información del usuario
            const usuario = usuariosDisponibles.find(r => r.id_usuario == idUsuario) || { nombre: 'Desconocido' };
            // Buscar información del rol
            const rol = rolesDisponibles.find(r => r.id_rol == idRol) || { nombre: 'Desconocido' };


            // Obtener permisos para este rol
            const permisosRol = permisosActuales.filter(p => p.id_rol == idRol);

            // Llenar datos en el modal de edición
            $("#edit_id_permiso").val(idRol);
            $("#edit_id_usuario_permiso").val(usuario.id_usuario);
            $("#edit_rol_permiso").val(rol.id_rol);

            // Generar módulos y acciones con los permisos actuales
            generarModulosAcciones("#edit_contenedor_modulos_acciones", permisosRol);

        } catch (error) {
            console.error("Error al cargar datos para edición:", error);
            Swal.fire("Error", "No se pudieron cargar los datos para editar", "error");
        }
    });

    // Evento para actualizar permiso
    $("#btn_actualizar_permiso").click(async function (e) {
        e.preventDefault();

        if (!validarFormularioPermiso(true)) return;

        try {
            const idUsuario = $("#edit_id_usuario_permiso").val();
            const idRol = $("#edit_id_permiso").val();
            const permisos = obtenerPermisosSeleccionados('#edit_contenedor_modulos_acciones');

            const formData = new FormData();
            formData.append('id_usuario', idUsuario);
            formData.append('id_rol', idRol);
            formData.append('permisos', JSON.stringify(permisos));
            formData.append('action', 'actualizar');

            const response = await fetchData("ajax/permiso.ajax.php", "POST", formData);

            if (response?.status) {
                Swal.fire("¡Correcto!", response.message || "Permisos actualizados correctamente", "success");
                $("#modal_editar_permiso").modal("hide");
                if ($.fn.DataTable.isDataTable("#tabla_permisos")) {
                    $("#tabla_permisos").DataTable().destroy();
                }
                await mostrarPermisos();
            } else {
                Swal.fire("Error", response?.message || "Error al actualizar los permisos", "error");
            }
        } catch (error) {
            console.error("Error al actualizar permiso:", error);
            Swal.fire("Error", "Ocurrió un error al actualizar el permiso", "error");
        }
    });


    /* ===========================================
    VER DETALLES MODULSO
    =========================================== */

    // Evento para ver permiso
    $("#tabla_permisos").on("click", ".btnVerPermiso", async function (e) {
        e.preventDefault();

        const idUsuario = $(this).data('id-usuario');
        const idRol = $(this).data('id-rol');

        try {
            // Buscar información del usuario
            const usuario = usuariosDisponibles.find(r => r.id_usuario == idUsuario) || { nombre: 'Desconocido' };
            // Buscar información del rol
            const rol = rolesDisponibles.find(r => r.id_rol == idRol) || { nombre: 'Desconocido' };

            // Obtener permisos para este rol
            const permisosRol = permisosActuales.filter(p => p.id_rol == idRol);

            // Llenar datos en el modal de visualización
            $("#view_usuario").val(usuario.nombre);
            $("#view_rol").val(rol.nombre);

            // Generar vista de módulos y acciones
            generarVistaModulosAcciones("#view_contenedor_modulos_acciones", permisosRol);

            // Mostrar modal
            $("#modal_ver_permiso").modal("show");

        } catch (error) {
            console.error("Error al cargar datos para visualización:", error);
            Swal.fire("Error", "No se pudieron cargar los datos para visualizar", "error");
        }
    });

    // Función para generar la vista de módulos y acciones (solo lectura)
    const generarVistaModulosAcciones = (contenedor, permisosRol = []) => {
        const $contenedor = $(contenedor);
        $contenedor.empty();

        // Ordenar módulos alfabéticamente
        const modulosOrdenados = [...modulosDisponibles].sort((a, b) => a.nombre.localeCompare(b.nombre));

        modulosOrdenados.forEach(modulo => {
            // Verificar si el módulo tiene permisos para este rol
            const moduloTienePermisos = permisosRol.some(p => p.id_modulo == modulo.id_modulo);

            // Si no tiene permisos, no mostrarlo
            if (!moduloTienePermisos) return;

            // Obtener acciones permitidas para este módulo y rol
            const accionesPermitidas = permisosRol
                .filter(p => p.id_modulo == modulo.id_modulo)
                .map(p => p.id_accion.toString());

            // Crear card para el módulo
            const card = $(`
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0">${modulo.nombre}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="view_acciones_modulo_${modulo.id_modulo}">
                            <!-- Acciones dinámicas -->
                        </div>
                    </div>
                </div>
            </div>
        `);

            $contenedor.append(card);

            // Generar acciones para este módulo
            generarVistaAccionesModulo(modulo.id_modulo, accionesPermitidas);
        });
    };

    // Generar vista de acciones para un módulo específico
    const generarVistaAccionesModulo = (idModulo, accionesSeleccionadas = []) => {
        const $contenedor = $(`#view_acciones_modulo_${idModulo}`);
        $contenedor.empty();

        // Filtrar acciones para este módulo
        const accionesModulo = accionesDisponibles.filter(accion =>
            accionesSeleccionadas.includes(accion.id_accion.toString())
        ).sort((a, b) => a.nombre.localeCompare(b.nombre));

        if (accionesModulo.length === 0) {
            $contenedor.append('<div class="col-12"><p class="text-muted mb-0">No hay acciones permitidas</p></div>');
            return;
        }

        accionesModulo.forEach(accion => {
            $contenedor.append(`
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span>${accion.nombre}</span>
                </div>
            </div>
        `);
        });
    };





    // Evento para eliminar permiso
    $("#tabla_permisos").on("click", ".btnEliminarPermiso", async function (e) {
        e.preventDefault();

        const idRol = $(this).data('id-rol');

        Swal.fire({
            title: "¿Está seguro?",
            text: "Esta acción eliminará todos los permisos para este rol",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('id_rol', idRol);
                    formData.append('action', 'eliminar');

                    const response = await fetchData("ajax/permiso.ajax.php", "POST", formData);

                    if (response?.status) {
                        Swal.fire("¡Eliminado!", response.message || "Permisos eliminados correctamente", "success");
                        if ($.fn.DataTable.isDataTable("#tabla_permisos")) {
                            $("#tabla_permisos").DataTable().destroy();
                        }
                        await mostrarPermisos();
                    } else {
                        Swal.fire("Error", response?.message || "Error al eliminar los permisos", "error");
                    }
                } catch (error) {
                    console.error("Error al eliminar permiso:", error);
                    Swal.fire("Error", "Ocurrió un error al eliminar el permiso", "error");
                }
            }
        });
    });

    // Evento al abrir el modal de nuevo permiso
    $('#modal_nuevo_permiso').on('shown.bs.modal', function () {
        // Generar módulos y acciones vacíos
        generarModulosAcciones("#contenedor_modulos_acciones");
    });

    // Inicializar
    cargarDatosIniciales();
});