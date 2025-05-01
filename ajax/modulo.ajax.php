<?php

require_once "../modelos/Modulo.modelo.php";
require_once "../controladores/Modulo.controlador.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    ControladorModulo::ctrMostrarModulos();
}