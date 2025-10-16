<?php
// public/index.php

// Incluir la conexión
require_once __DIR__ . '/../config.php';

// Incluir controladores cuando los tengamos
// Por ahora, mostramos un menú básico

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGH - Hotel El Gran Descanso</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        a { display: block; margin: 10px 0; padding: 10px; background: #f0f0f0; text-decoration: none; color: #333; }
        a:hover { background: #e0e0e0; }
    </style>
</head>
<body>
<h1>Sistema de Gestión Hotelera</h1>

<h2>Vista Pública</h2>
<a href="../vista_publica/reservar.php">➕ Hacer una reserva</a>

<h2>Panel de Administración</h2>
<a href="../vista_admin/habitaciones.php">🛏Gestionar habitaciones</a>
<a href="../vista_admin/huespedes.php">Gestionar huéspedes</a>
<a href="../vista_admin/reservas.php">Ver reservas</a>
<a href="../vista_admin/mantenimiento.php">Tareas de mantenimiento</a>
</body>
</html>