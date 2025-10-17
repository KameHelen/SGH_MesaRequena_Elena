<?php
// vista/admin/habitaciones.php
// Recibe: $habitaciones, $mensaje, $tipo_mensaje
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Habitaciones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f8f9fa; }
        .container { max-width: 900px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .alert.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f1f3f5; }
        .estado-Limpia { color: #27ae60; font-weight: bold; }
        .estado-Sucia { color: #e74c3c; font-weight: bold; }
        .estado-En\ Limpieza { color: #e67e22; font-weight: bold; }
        select { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛏️ Gestión de Habitaciones</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <p>Actualiza el estado de limpieza de cada habitación según corresponda.</p>

        <table>
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Precio/N</th>
                    <th>Estado de Limpieza</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($habitaciones as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['numero']) ?></td>
                        <td><?= htmlspecialchars($h['tipo']) ?></td>
                        <td>$<?= number_format($h['precio_base'], 2) ?></td>
                        <td>
                            <?php
                            $clase = 'estado-' . str_replace(' ', '\ ', $h['estado_limpieza']);
                            echo "<span class='$clase'>{$h['estado_limpieza']}</span>";
                            ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="actualizar_limpieza">
                                <input type="hidden" name="habitacion_id" value="<?= $h['id'] ?>">
                                <select name="estado_limpieza">
                                    <option value="Limpia" <?= $h['estado_limpieza'] === 'Limpia' ? 'selected' : '' ?>>Limpia</option>
                                    <option value="Sucia" <?= $h['estado_limpieza'] === 'Sucia' ? 'selected' : '' ?>>Sucia</option>
                                    <option value="En Limpieza" <?= $h['estado_limpieza'] === 'En Limpieza' ? 'selected' : '' ?>>En Limpieza</option>
                                </select>
                                <button type="submit">Guardar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="../public/index.php" class="back">← Volver al inicio</a>
    </div>
</body>
</html>