<?php
// vista_publica/reservar.php

global $pdo;
require_once __DIR__ . '/../config.php';

// Cargar datos para los desplegables
try {
    $huespedes = $pdo->query("SELECT id, nombre FROM huespedes")->fetchAll();
    $habitaciones = $pdo->query("SELECT id, numero, tipo, precio_base FROM habitaciones")->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar datos: " . htmlspecialchars($e->getMessage()));
}

$mensaje = '';
$tipo_mensaje = ''; // 'exito' o 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $huesped_id = (int)$_POST['huesped_id'];
        $habitacion_id = (int)$_POST['habitacion_id'];
        $fecha_llegada = $_POST['fecha_llegada'];
        $fecha_salida = $_POST['fecha_salida'];
        $precio_base = (float)$_POST['precio_base'];

        // Validar que las fechas sean válidas
        $hoy = date('Y-m-d');
        if ($fecha_llegada < $hoy) {
            throw new Exception("La fecha de llegada no puede ser anterior a hoy.");
        }
        if ($fecha_salida <= $fecha_llegada) {
            throw new Exception("La fecha de salida debe ser posterior a la de llegada.");
        }

        // 1. Verificar solapamiento con reservas CONFIRMADAS
        $sql1 = "
            SELECT COUNT(*) 
            FROM reservas 
            WHERE habitacion_id = :habitacion_id 
            AND estado = 'Confirmada'
            AND fecha_llegada < :fecha_salida 
            AND fecha_salida > :fecha_llegada
        ";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':habitacion_id' => $habitacion_id,
            ':fecha_salida' => $fecha_salida,
            ':fecha_llegada' => $fecha_llegada
        ]);
        if ($stmt1->fetchColumn() > 0) {
            throw new Exception("La habitación ya tiene una reserva confirmada en esas fechas.");
        }

        // 2. Verificar mantenimiento ACTIVO en esas fechas
        $sql2 = "
            SELECT COUNT(*) 
            FROM tareas_mantenimiento 
            WHERE habitacion_id = :habitacion_id 
            AND estado = 'Activa'
            AND fecha_inicio <= :fecha_salida 
            AND fecha_fin >= :fecha_llegada
        ";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':habitacion_id' => $habitacion_id,
            ':fecha_salida' => $fecha_salida,
            ':fecha_llegada' => $fecha_llegada
        ]);
        if ($stmt2->fetchColumn() > 0) {
            throw new Exception("La habitación tiene una tarea de mantenimiento activa en esas fechas.");
        }

        // Calcular precio total
        $dias = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / (60 * 60 * 24);
        $precio_total = $dias * $precio_base;

        // Insertar reserva (estado: Pendiente)
        $sql3 = "
            INSERT INTO reservas (huesped_id, habitacion_id, fecha_llegada, fecha_salida, precio_total, estado)
            VALUES (:huesped_id, :habitacion_id, :fecha_llegada, :fecha_salida, :precio_total, 'Pendiente')
        ";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->execute([
            ':huesped_id' => $huesped_id,
            ':habitacion_id' => $habitacion_id,
            ':fecha_llegada' => $fecha_llegada,
            ':fecha_salida' => $fecha_salida,
            ':precio_total' => $precio_total
        ]);

        $mensaje = "✅ Reserva creada con éxito. Estado: Pendiente.";
        $tipo_mensaje = 'exito';

    } catch (Exception $e) {
        $mensaje = "❌ " . htmlspecialchars($e->getMessage());
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva - SGH</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; }
        .container { max-width: 600px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #34495e; }
        select, input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 20px; padding: 12px 20px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #219653; }
        .alert { padding: 12px; margin: 15px 0; border-radius: 4px; }
        .alert.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h2>➕ Nueva Reserva</h2>

    <?php if ($mensaje): ?>
        <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="huesped_id">Huésped:</label>
        <select name="huesped_id" required>
            <option value="">-- Seleccione un huésped --</option>
            <?php foreach ($huespedes as $h): ?>
                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="habitacion_id">Habitación:</label>
        <select name="habitacion_id" required onchange="setPrecioBase(this)">
            <option value="">-- Seleccione una habitación --</option>
            <?php foreach ($habitaciones as $h): ?>
                <option value="<?= $h['id'] ?>" data-precio="<?= $h['precio_base'] ?>">
                    <?= htmlspecialchars($h['numero']) ?> (<?= $h['tipo'] ?>) - $<?= number_format($h['precio_base'], 2) ?>/noche
                </option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" id="precio_base" name="precio_base" value="0">

        <label for="fecha_llegada">Fecha de llegada:</label>
        <input type="date" name="fecha_llegada" required min="<?= date('Y-m-d') ?>">

        <label for="fecha_salida">Fecha de salida:</label>
        <input type="date" name="fecha_salida" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">

        <button type="submit">Crear Reserva</button>
    </form>

    <a href="../public/index.php" class="back">← Volver al inicio</a>
</div>

<script>
    function setPrecioBase(select) {
        const option = select.options[select.selectedIndex];
        const precio = option.getAttribute('data-precio') || 0;
        document.getElementById('precio_base').value = precio;
    }
</script>
</body>
</html>