<?php

require_once "../controladores/Trabajador.controlador.php";
require_once "../modelos/Trabajador.modelo.php";

$item = null;
$valor = null;
$mostrarTrabajadores = ControladorTrabajador::ctrMostrarTrabajadores($item, $valor);
$tablaTrabajador = array();
foreach ($mostrarTrabajadores as $key => $trabajador) {
    $fila = array(
        'id_trabajador' => $trabajador['id_trabajador'],
        'nombre' => $trabajador['nombre'],
        'num_documento' => $trabajador['num_documento'],
        'telefono' => $trabajador['telefono'],
        'correo' => $trabajador['correo'],
        'foto' => $trabajador['foto'],
        'cv' => $trabajador['cv'],
        'tipo_pago' => $trabajador['tipo_pago'],
        'num_cuenta' => $trabajador['num_cuenta'],
        'estado_trabajador' => $trabajador['estado_trabajador']
    );
    $tablaTrabajador[] = $fila;
}
echo json_encode($tablaTrabajador);
