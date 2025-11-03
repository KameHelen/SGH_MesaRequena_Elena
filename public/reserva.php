<?php
// public/reserva.php
session_start();
require_once __DIR__ . '/../idioma.php';

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/index.php");
    exit;
}

require_once __DIR__ . '/../config.php';

// Verificar si YA es huésped
$email_usuario = $_SESSION['email'];
$stmt = $pdo->prepare("SELECT id, documento_identidad FROM huespedes WHERE email = ?");
$stmt->execute([$email_usuario]);
$huesped_existente = $stmt->fetch();

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $habitacion_id = (int)$_POST['habitacion_id'];
        $fecha_llegada = $_POST['fecha_llegada'];
        $fecha_salida = $_POST['fecha_salida'];
        $precio_base = (float)$_POST['precio_base'];

        if ($fecha_salida <= $fecha_llegada) {
            throw new Exception("La fecha de salida debe ser posterior a la de llegada.");
        }

        // Verificar disponibilidad
        $sql1 = "SELECT COUNT(*) FROM reservas WHERE habitacion_id = ? AND estado = 'Confirmada' AND fecha_llegada < ? AND fecha_salida > ?";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt1->fetchColumn() > 0) {
            throw new Exception("La habitación ya está reservada en esas fechas.");
        }

        $sql2 = "SELECT COUNT(*) FROM tareas_mantenimiento WHERE habitacion_id = ? AND estado = 'Activa' AND fecha_inicio <= ? AND fecha_fin >= ?";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt2->fetchColumn() > 0) {
            throw new Exception("La habitación tiene mantenimiento activo en esas fechas.");
        }

        // GESTIÓN DEL HUÉSPED
        if (!$huesped_existente) {
            // Es la PRIMERA RESERVA - necesitamos el DNI
            $documento_identidad = trim($_POST['documento_identidad'] ?? '');
            if (empty($documento_identidad)) {
                throw new Exception("El documento de identidad es obligatorio para tu primera reserva.");
            }
            
            // Crear nuevo huésped
            $pdo->prepare("INSERT INTO huespedes (nombre, email, documento_identidad) VALUES (?, ?, ?)")
                ->execute([$_SESSION['nombreUsuario'], $email_usuario, $documento_identidad]);
            $huesped_id = $pdo->lastInsertId();
        } else {
            // Ya es huésped - usar el existente
            $huesped_id = $huesped_existente['id'];
        }

        // Crear la reserva
        $dias = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / 86400;
        $precio_total = $dias * $precio_base;
        $pdo->prepare("INSERT INTO reservas (huesped_id, habitacion_id, fecha_llegada, fecha_salida, precio_total, estado) VALUES (?, ?, ?, ?, ?, 'Pendiente')")
            ->execute([$huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $precio_total]);
        
        $mensaje = "<div class='alert success'>✅ Reserva creada con éxito.</div>";
        
        // Refrescar el estado de huésped para la vista
        $stmt = $pdo->prepare("SELECT id, documento_identidad FROM huespedes WHERE email = ?");
        $stmt->execute([$email_usuario]);
        $huesped_existente = $stmt->fetch();

    } catch (Exception $e) {
        $mensaje = "<div class='alert error'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$habitaciones = $pdo->query("SELECT id, numero, tipo, precio_base FROM habitaciones")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva - Hotel El Gran Descanso</title>
  <style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        margin: 0; 
        background: linear-gradient(135deg, #f5f0ff 0%, #e8e1ff 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .container { 
        background: white; 
        padding: 30px; 
        border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(142, 68, 173, 0.2); 
        width: 100%;
        max-width: 600px;
        border: 1px solid #e6d7f5;
    }
    h2 { 
        color: #8e44ad; 
        text-align: center; 
        margin-bottom: 25px; 
        font-weight: 600;
        border-bottom: 2px solid #f0e6ff;
        padding-bottom: 15px;
    }
    .form-group { 
        margin: 20px 0; 
    }
    label { 
        display: block; 
        font-weight: 600; 
        margin-bottom: 8px; 
        color: #2c3e50;
        font-size: 14px;
    }
    select, input { 
        width: 100%; 
        padding: 12px; 
        border: 2px solid #e6d7f5; 
        border-radius: 8px; 
        font-size: 15px;
        transition: border-color 0.3s;
    }
    select:focus, input:focus { 
        outline: none; 
        border-color: #8e44ad; 
        box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
    }
    button { 
        background: #8e44ad; 
        color: white; 
        padding: 14px 25px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 16px; 
        font-weight: 600;
        width: 100%;
        transition: background 0.3s;
    }
    button:hover { 
        background: #732d91; 
    }
    .user-info { 
        background: #f8f4ff; 
        padding: 15px; 
        border-radius: 10px; 
        margin-bottom: 25px; 
        border-left: 4px solid #8e44ad;
        text-align: center;
    }
    .alert { 
        padding: 12px; 
        margin-bottom: 20px; 
        border-radius: 8px; 
        font-weight: 500;
        text-align: center;
    }
    .success { 
        background: #e8f5e9; 
        color: #27ae60; 
        border: 1px solid #c8e6c9;
    }
    .error { 
        background: #ffebee; 
        color: #e74c3c; 
        border: 1px solid #ffcdd2;
    }
    .logout-btn {
        display: inline-block; 
        padding: 10px 20px; 
        background: #e74c3c; 
        color: white; 
        text-decoration: none; 
        border-radius: 6px;
        font-weight: 600;
        transition: background 0.3s;
    }
    .logout-btn:hover {
        background: #c0392b;
    }
</style>
</head>
<body>
    <div class="container">
        <div class="user-info">
            <strong>👤 Usuario:</strong> <?= htmlspecialchars($_SESSION['nombreUsuario']) ?> 
            (<?= htmlspecialchars($_SESSION['email']) ?>)
            <?php if ($huesped_existente): ?>
                <br><small style="color: #27ae60;">✅ Ya eres huésped registrado</small>
            <?php else: ?>
                <br><small style="color: #e74c3c;">⚠️ Primera reserva: necesitamos tu documento de identidad</small>
            <?php endif; ?>
        </div>
        
        <h2>➕ Nueva Reserva</h2>
        <?= $mensaje ?>
        <form method="POST">
            <?php if (!$huesped_existente): ?>
                <div class="form-group">
                    <label for="documento_identidad">Documento de Identidad *</label>
                    <input type="text" id="documento_identidad" name="documento_identidad" 
                           value="<?= htmlspecialchars($_POST['documento_identidad'] ?? '') ?>" 
                           placeholder="Ej: 12345678A" required>
                </div>
            <?php endif; ?>

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
    
    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombreUsuario']) ?></strong> (Usuario)</p>
        <a href="../cerrar_sesion.php" style="display: inline-block; padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px;">
            🔒 Cerrar Sesión
        </a>
    </div>
    
    <script>
        function setPrecio(sel) {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('precio_base').value = opt.getAttribute('data-precio') || 0;
        }
    </script>
    <div style="position: fixed; top: 20px; right: 20px; background: white; padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <a href="?lang=es" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'es' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇪🇸 ES</a>
    <a href="?lang=en" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'en' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇬🇧 EN</a>
</div>
</body>
</html>