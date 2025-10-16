<?php
// vista_admin/reservas.php

require_once __DIR__ . '/../config.php';

// Si se envía una acción (confirmar o cancelar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    try {
        $reserva_id = (int)$_POST['reserva_id'];
        $accion = $_POST['accion'];

        if ($accion === 'confirmar') {
            $nuevo_estado = 'Confirmada';
        } elseif ($accion === 'cancelar') {
            $nuevo_estado = 'Cancelada';
        } else {
            throw new Exception("Acción no válida.");
        }

        // Actualizar estado de la reserva
        $sql = "UPDATE reservas SET estado = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$nuevo_estado, $reserva_id]);

        $mensaje = "✅ Reserva #$reserva_id $accion correctamente.";
        $tipo_mensaje = 'exito';

    } catch (Exception $e) {
        $mensaje = "❌ Error: " . htmlspecialchars($e->getMessage());
        $tipo_mensaje = 'error';
    }
}

// Cargar todas las reservas con datos del huésped y habitación
$sql = "
    SELECT 
        r.id,
        r.fecha_llegada,
        r.fecha_salida,
        r.precio_total,
        r.estado,
        r.fecha_reserva,
        h.nombre AS huesped,
        hab.numero AS habitacion_numero,
        hab.tipo AS habitacion_tipo
    FROM reservas r
    JOIN huespedes h ON r.huesped_id = h.id
    JOIN habitaciones hab ON r.habitacion_id = hab.id
    ORDER BY r.fecha_reserva DESC
";
$reservas = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Reservas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f8f9fa; }
        .container { max-width: 1000px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .alert.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f1f3f5; }
        .estado-pendiente { color: #e67e22; font-weight: bold; }
        .estado-confirmada { color: #27ae60; font-weight: bold; }
        .estado-cancelada { color: #e74c3c; font-weight: bold; text-decoration: line-through; }
        .acciones form { display: inline; margin-right: 10px; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-confirmar { background: #27ae60; }
        .btn-cancelar { background: #e74c3c; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 Gestión de Reservas</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if (count($reservas) === 0): ?>
            <p>No hay reservas registradas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Huésped</th>
                        <th>Habitación</th>
                        <th>Fechas</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['huesped']) ?></td>
                            <td><?= htmlspecialchars($r['habitacion_numero']) ?> (<?= $r['habitacion_tipo'] ?>)</td>
                            <td><?= $r['fecha_llegada'] ?> → <?= $r['fecha_salida'] ?></td>
                            <td>$<?= number_format($r['precio_total'], 2) ?></td>
                            <td>
                                <?php
                                $clase = match($r['estado']) {
                                    'Pendiente' => 'estado-pendiente',
                                    'Confirmada' => 'estado-confirmada',
                                    'Cancelada' => 'estado-cancelada',
                                };
                                echo "<span class='$clase'>{$r['estado']}</span>";
                                ?>
                            </td>
                            <td class="acciones">
                                <?php if ($r['estado'] === 'Pendiente'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="accion" value="confirmar">
                                        <button type="submit" class="btn btn-confirmar" 
                                                onclick="return confirm('¿Confirmar esta reserva?')">
                                            Confirmar
                                        </button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="accion" value="cancelar">
                                        <button type="submit" class="btn btn-cancelar"
                                                onclick="return confirm('¿Cancelar esta reserva?')">
                                            Cancelar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <em>Sin acciones</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="../public/index.php" class="back">← Volver al inicio</a>
    </div>
</body>
</html>