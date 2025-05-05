<?php
require_once "../controladores/Asistencia.controlador.php";
require_once "../modelos/Asistencia.modelo.php";

// Verificar si se están enviando filtros
$tieneFiltros = (
    (isset($_POST["filtro_trabajador_asistencia"]) && !empty($_POST["filtro_trabajador_asistencia"])) ||
    (isset($_POST["filtro_estado_asistencia"]) && !empty($_POST["filtro_estado_asistencia"])) ||
    (isset($_POST["filtro_fecha_desde_asistencia"]) && !empty($_POST["filtro_fecha_desde_asistencia"])) ||
    (isset($_POST["filtro_fecha_hasta_asistencia"]) && !empty($_POST["filtro_fecha_hasta_asistencia"]))
);

if ($tieneFiltros) {
    // Si hay filtros, usar el método con filtros
    $respuesta = ControladorAsistencia::ctrReporteAsistenciaTable();
} else {
    // Si no hay filtros, usar el método sin filtros
    $item = null;
    $valor = null;
    $respuesta = ControladorAsistencia::ctrMostrarReporteAsistencia($item, $valor);
}

// Preparar la respuesta
$tabla = array();
foreach ($respuesta as $asistencia) {
    $fila = array(
        'id_asistencia' => $asistencia['id_asistencia'],
        'nombre' => $asistencia['nombre'],
        'fecha_asistencia' => $asistencia['fecha_asistencia'],
        'hora_entrada' => $asistencia['hora_entrada'],
        'hora_salida' => $asistencia['hora_salida'],
        'estado' => $asistencia['estado'],
        'observaciones' => $asistencia['observaciones']
    );
    $tabla[] = $fila;
}

// Devolver la respuesta en formato JSON
echo json_encode($tabla);