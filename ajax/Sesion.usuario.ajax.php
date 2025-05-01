<?php
session_start();

if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] === "ok") {
    // Procesar las acciones para convertirlas a la estructura deseada
    $accionesOriginales = is_array($_SESSION["acciones"]) ? $_SESSION["acciones"] : explode(',', $_SESSION["acciones"]);
    $accionesOrganizadas = [];
    
    foreach ($accionesOriginales as $accion) {
        // Separar el módulo de la acción (ej: "arqueos_caja: activar")
        $partes = explode(': ', $accion);
        if (count($partes) === 2) {
            $modulo = $partes[0];
            $accionNombre = $partes[1];
            
            if (!isset($accionesOrganizadas[$modulo])) {
                $accionesOrganizadas[$modulo] = [];
            }
            
            $accionesOrganizadas[$modulo][] = $accionNombre;
        }
    }
    
    $datosSesion = [
        "id_usuario" => $_SESSION["id_usuario"],
        "nombre_usuario" => $_SESSION["nombre_usuario"],
        "usuario" => $_SESSION["usuario"],
        "imagen_usuario" => $_SESSION["imagen_usuario"],
        "modulos" => is_array($_SESSION["modulos"]) ? $_SESSION["modulos"] : explode(',', $_SESSION["modulos"]),
        "acciones" => $accionesOrganizadas
    ];

    echo json_encode([
        "estado" => "success",
        "mensaje" => "Sesión iniciada",
        "datos" => $datosSesion
    ]);
} else {
    echo json_encode([
        "estado" => "error",
        "mensaje" => "No hay sesión activa"
    ]);
}