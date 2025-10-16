<?php
// public/huespedes.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controlador/HuespedController.php';

$controller = new HuespedController($pdo);
$controller->index();
?>