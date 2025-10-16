<?php
// public/mantenimiento.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controlador/MantenimientoController.php';

$controller = new MantenimientoController($pdo);
$controller->index();
?>