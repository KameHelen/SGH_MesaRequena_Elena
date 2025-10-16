<?php
// public/habitaciones.php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controlador/HabitacionController.php';

$controller = new HabitacionController($pdo);
$controller->index();
?>