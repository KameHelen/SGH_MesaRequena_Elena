<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controlador/ReservaAdminController.php';

$controller = new ReservaAdminController($pdo);
$controller->index();
?>