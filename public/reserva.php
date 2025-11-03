<?php
// public/reserva.php


session_start();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../login.php");
    exit;
}
require_once __DIR__ . '/../config.php';


$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $huesped_id = (int)$_POST['huesped_id'];
        $habitacion_id = (int)$_POST['habitacion_id'];
        $fecha_llegada = $_POST['fecha_llegada'];
        $fecha_salida = $_POST['fecha_salida'];
        $precio_base = (float)$_POST['precio_base'];

        if ($fecha_salida <= $fecha_llegada) {
            throw new Exception("La fecha de salida debe ser posterior a la de llegada.");
        }

        // Verificar solapamiento con reservas confirmadas
        $sql1 = "SELECT COUNT(*) FROM reservas WHERE habitacion_id = ? AND estado = 'Confirmada' AND fecha_llegada < ? AND fecha_salida > ?";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt1->fetchColumn() > 0) {
            throw new Exception("La habitación ya está reservada en esas fechas.");
        }

        // Verificar mantenimiento activo
        $sql2 = "SELECT COUNT(*) FROM tareas_mantenimiento WHERE habitacion_id = ? AND estado = 'Activa' AND fecha_inicio <= ? AND fecha_fin >= ?";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt2->fetchColumn() > 0) {
            throw new Exception("La habitación tiene mantenimiento activo en esas fechas.");
        }

        $dias = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / 86400;
        $precio_total = $dias * $precio_base;

        $sql3 = "INSERT INTO reservas (huesped_id, habitacion_id, fecha_llegada, fecha_salida, precio_total, estado) VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $pdo->prepare($sql3)->execute([$huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $precio_total]);
        $mensaje = "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Reserva creada con éxito.</div>";

    } catch (Exception $e) {
        $mensaje = "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$huespedes = $pdo->query("SELECT id, nombre FROM huespedes")->fetchAll();
$habitaciones = $pdo->query("SELECT id, numero, tipo, precio_base FROM habitaciones")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva - Hotel El Gran Descanso</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; }
        .container { max-width: 600px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        select, input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>➕ Nueva Reserva</h2>
        <?= $mensaje ?>
        <form method="POST">
            <div class="form-group">
                <label>Huésped:</label>
                <select name="huesped_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($huespedes as $h): ?>
                        <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Habitación:</label>
                <select name="habitacion_id" required onchange="setPrecio(this)">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($habitaciones as $h): ?>
                        <option value="<?= $h['id'] ?>" data-precio="<?= $h['precio_base'] ?>">
                            <?= htmlspecialchars($h['numero']) ?> (<?= $h['tipo'] ?>) - $<?= $h['precio_base'] ?>/noche
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="precio_base" id="precio_base" value="0">
            <div class="form-group">
                <label>Fecha de llegada:</label>
                <input type="date" name="fecha_llegada" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Fecha de salida:</label>
                <input type="date" name="fecha_salida" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            <button type="submit">Crear Reserva</button>
        </form>
    </div>
    <script>
        function setPrecio(sel) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('precio_base').value = opt.getAttribute('data-precio') || 0;
        }
    </script>
</body>
</html>