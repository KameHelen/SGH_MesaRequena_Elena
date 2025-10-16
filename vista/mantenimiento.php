<?php
// vista/admin/mantenimiento.php
// Recibe: $habitaciones, $tareas, $mensaje, $tipo_mensaje
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Mantenimiento</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f8f9fa; }
        .container { max-width: 900px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .alert.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        select, input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        button { padding: 12px 20px; background: #8e44ad; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #732d91; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f1f3f5; }
        .estado-Activa { color: #e67e22; font-weight: bold; }
        .estado-Completada { color: #27ae60; font-weight: bold; }
        .estado-Cancelada { color: #95a5a6; font-weight: bold; text-decoration: line-through; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔧 Gestión de Mantenimiento</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <h3>➕ Registrar Nueva Tarea de Mantenimiento</h3>
        <form method="POST">
            <div class="form-group">
                <label for="habitacion_id">Habitación:</label>
                <select id="habitacion_id" name="habitacion_id" required>
                    <option value="">-- Seleccione una habitación --</option>
                    <?php foreach ($habitaciones as $h): ?>
                        <option value="<?= $h['id'] ?>">
                            <?= htmlspecialchars($h['numero']) ?> (<?= $h['tipo'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin esperada:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción de la tarea:</label>
                <textarea id="descripcion" name="descripcion" required placeholder="Ej: Arreglar grifo, Cambiar bombilla, etc."></textarea>
            </div>
            <button type="submit">Registrar Tarea</button>
        </form>

        <h3 style="margin-top: 40px;">📋 Tareas Registradas (<?= count($tareas) ?>)</h3>
        <?php if (count($tareas) === 0): ?>
            <p>No hay tareas de mantenimiento registradas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Habitación</th>
                        <th>Descripción</th>
                        <th>Fechas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $t): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['habitacion_numero']) ?> (<?= $t['habitacion_tipo'] ?>)</td>
                            <td><?= htmlspecialchars($t['descripcion']) ?></td>
                            <td><?= $t['fecha_inicio'] ?> → <?= $t['fecha_fin'] ?></td>
                            <td>
                                <?php
                                $clase = match($t['estado']) {
                                    'Activa' => 'estado-Activa',
                                    'Completada' => 'estado-Completada',
                                    'Cancelada' => 'estado-Cancelada',
                                };
                                echo "<span class='$clase'>{$t['estado']}</span>";
                                ?>
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