<?php
// bienvenida.php
session_start();

// Verificar si está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: login.php");
    exit;
}

// Depuración temporal: ver qué rol tiene el usuario
// echo "Rol actual: " . $_SESSION['rol'] . "<br>";
// exit;

// Redirigir según el rol
if ($_SESSION['rol'] === 'admin') {
    header("Location: admin/index.php");
    exit;
} elseif ($_SESSION['rol'] === 'usuario') {
    header("Location: public/reserva.php");
    exit;
} else {
    // Rol desconocido - redirigir al login
    session_destroy();
    header("Location: login.php");
    exit;
}
?>