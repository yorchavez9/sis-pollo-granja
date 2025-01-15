<?php
session_start();

// Verificar si la sesión está iniciada
if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] === "ok") {
    // Preparar los datos de sesión para enviar como JSON
    $datosSesion = [
        "id_usuario" => $_SESSION["id_usuario"],
        "nombre_usuario" => $_SESSION["nombre_usuario"],
        "usuario" => $_SESSION["usuario"],
        "imagen_usuario" => $_SESSION["imagen_usuario"]
    ];

    // Enviar respuesta JSON
    echo json_encode([
        "estado" => "success",
        "mensaje" => "Sesión iniciada",
        "datos" => $datosSesion
    ]);
} else {
    // En caso de que no haya sesión activa
    echo json_encode([
        "estado" => "error",
        "mensaje" => "No hay sesión activa"
    ]);
}
