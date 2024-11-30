<?php

require_once "../controladores/Asistencia.controlador.php";
require_once "../modelos/Asistencia.modelo.php";

if (
    (isset($_POST["filtro_trabajador_asistencia"]) && !empty($_POST["filtro_trabajador_asistencia"])) ||
    (isset($_POST["filtro_estado_asistencia"]) && !empty($_POST["filtro_estado_asistencia"])) ||
    (isset($_POST["filtro_fecha_desde_asistencia"]) && !empty($_POST["filtro_fecha_desde_asistencia"])) ||
    (isset($_POST["filtro_fecha_hasta_asistencia"]) && !empty($_POST["filtro_fecha_hasta_asistencia"]))
) {

    $respuesta = ControladorAsistencia::ctrReporteAsistenciaTable();
    echo json_encode($respuesta);
} else {
    $item = null;
    $valor = null;
    $asistencias = ControladorAsistencia::ctrMostrarReporteAsistencia($item, $valor);

    $tabla = array();

    foreach ($asistencias as $key => $asistencia) {

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


    echo json_encode($tabla);
}
