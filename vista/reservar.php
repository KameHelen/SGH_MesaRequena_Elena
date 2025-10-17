<?php

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
    </style>
</head>
<body>
<div class="container">
    <h2>➕ Nueva Reserva</h2>

    <?php if (!empty($mensaje)): ?>
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