<?php
// vista_admin/huespedes.php

require_once __DIR__ . '/../config.php';

$mensaje = '';
$tipo_mensaje = '';

// Si se envía el formulario de nuevo huésped
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $documento = trim($_POST['documento']);

        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($documento)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido.");
        }

        // Verificar que el email no exista ya
        $stmt = $pdo->prepare("SELECT id FROM huespedes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Ya existe un huésped con ese email.");
        }

        // Insertar nuevo huésped
        $sql = "INSERT INTO huespedes (nombre, email, documento_identidad) VALUES (?, ?, ?)";
        $pdo->prepare($sql)->execute([$nombre, $email, $documento]);

        $mensaje = "Huésped registrado correctamente.";
        $tipo_mensaje = 'exito';

        // Limpiar campos después de guardar
        $_POST = [];

    } catch (Exception $e) {
        $mensaje = "❌ " . htmlspecialchars($e->getMessage());
        $tipo_mensaje = 'error';
    }
}

// Cargar todos los huéspedes
$huespedes = $pdo->query("SELECT id, nombre, email, documento_identidad FROM huespedes ORDER BY nombre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Huéspedes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f8f9fa; }
        .container { max-width: 900px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .alert.exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 12px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f1f3f5; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>👤 Gestión de Huéspedes</h2>

        <?php if ($mensaje): ?>
            <div class="alert <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <!-- Formulario para nuevo huésped -->
        <h3>➕ Registrar Nuevo Huésped</h3>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email (único):</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="documento">Documento de identidad:</label>
                <input type="text" id="documento" name="documento" value="<?= htmlspecialchars($_POST['documento'] ?? '') ?>" required>
            </div>
            <button type="submit">Registrar Huésped</button>
        </form>

        <!-- Lista de huéspedes -->
        <h3 style="margin-top: 40px;">📋 Huéspedes Registrados (<?= count($huespedes) ?>)</h3>
        <?php if (count($huespedes) === 0): ?>
            <p>No hay huéspedes registrados.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Documento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($huespedes as $h): ?>
                        <tr>
                            <td><?= $h['id'] ?></td>
                            <td><?= htmlspecialchars($h['nombre']) ?></td>
                            <td><?= htmlspecialchars($h['email']) ?></td>
                            <td><?= htmlspecialchars($h['documento_identidad']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="../public/index.php" class="back">← Volver al inicio</a>
    </div>
</body>
</html>