<?php

require_once "../modelos/Accion.modelo.php";
require_once "../controladores/Accion.controlador.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    ControladorAccion::ctrMostrarAcciones();
}