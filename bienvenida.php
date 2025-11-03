<?php
// bienvenida.php
session_start();

// Verificar si está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['nombreUsuario'];
$rol = $_SESSION['rol'];

// Redirigir según el rol
if ($rol === 'admin') {
    header("Location: admin/index.php");
    exit;
} else {
    header("Location: public/reserva.php");
    exit;
}
?>