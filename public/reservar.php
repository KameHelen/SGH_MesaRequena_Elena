<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controlador/ReservaPublicaController.php';

$controller = new ReservaPublicaController($pdo);
$controller->crear();
?>