<?php
// admin/index.php
session_start();
require_once __DIR__ . '/../idioma.php'; 

// Proteger la página
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config.php';

// ======================
// LÓGICA DE USUARIOS REGISTRADOS (todos los que pueden hacer login)
// ======================
$usuarios_registrados = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre")->fetchAll();

// ======================
// LÓGICA DE HUÉSPEDES REALES (solo quienes han hecho reservas)
// ======================
$huespedes_reales = $pdo->query("
    SELECT DISTINCT h.id, h.nombre, h.email, h.documento_identidad
    FROM huespedes h
    INNER JOIN reservas r ON h.id = r.huesped_id
    ORDER BY h.nombre
")->fetchAll();

// ======================
// LÓGICA DE RESERVAS (ADMIN)
// ======================
$mensaje_reservas_admin = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && in_array($_POST['accion'], ['confirmar', 'cancelar'])) {
    try {
        $reserva_id = (int)$_POST['reserva_id'];
        $estado = ($_POST['accion'] === 'confirmar') ? 'Confirmada' : 'Cancelada';
        $sql = "UPDATE reservas SET estado = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$estado, $reserva_id]);
        $mensaje_reservas_admin = "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Reserva actualizada correctamente.</div>";

    } catch (Exception $e) {
        $mensaje_reservas_admin = "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ Error al actualizar.</div>";
    }
}
$reservas = $pdo->query("
    SELECT r.*, h.nombre AS huesped, hab.numero AS habitacion 
    FROM reservas r
    JOIN huespedes h ON r.huesped_id = h.id
    JOIN habitaciones hab ON r.habitacion_id = hab.id
    ORDER BY r.fecha_reserva DESC
")->fetchAll();

// ======================
// LÓGICA DE HABITACIONES (LIMPIEZA)
// ======================
$mensaje_habitaciones = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_limpieza') {
    try {
        $habitacion_id = (int)$_POST['habitacion_id'];
        $estado = $_POST['estado_limpieza'];
        $estados_validos = ['Limpia', 'Sucia', 'En Limpieza'];
        if (!in_array($estado, $estados_validos)) {
            throw new Exception("Estado no válido.");
        }
        $sql = "UPDATE habitaciones SET estado_limpieza = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$estado, $habitacion_id]);
        $mensaje_habitaciones = "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Estado de limpieza actualizado.</div>";

    } catch (Exception $e) {
        $mensaje_habitaciones = "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
$habitaciones = $pdo->query("SELECT * FROM habitaciones ORDER BY numero")->fetchAll();

// ======================
// LÓGICA DE MANTENIMIENTO
// ======================
$mensaje_mantenimiento = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_mantenimiento') {
    try {
        $habitacion_id = (int)$_POST['habitacion_id'];
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';

        if (empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin)) {
            throw new Exception("Todos los campos son obligatorios.");
        }
        if ($fecha_fin < $fecha_inicio) {
            throw new Exception("La fecha de fin debe ser posterior a la de inicio.");
        }

        $sql = "INSERT INTO tareas_mantenimiento (habitacion_id, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, 'Activa')";
        $pdo->prepare($sql)->execute([$habitacion_id, $descripcion, $fecha_inicio, $fecha_fin]);
        $mensaje_mantenimiento = "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Tarea de mantenimiento registrada.</div>";

    } catch (Exception $e) {
        $mensaje_mantenimiento = "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
$habitaciones_mant = $pdo->query("SELECT id, numero, tipo FROM habitaciones ORDER BY numero")->fetchAll();
$tareas_mantenimiento = $pdo->query("
    SELECT tm.*, h.numero AS habitacion 
    FROM tareas_mantenimiento tm
    JOIN habitaciones h ON tm.habitacion_id = h.id
    ORDER BY tm.fecha_inicio DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - SGH</title>
    <style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        margin: 0; 
        background: linear-gradient(135deg, #f5f0ff 0%, #e8e1ff 100%);
        padding: 20px;
    }
    .container { 
        max-width: 1200px; 
        margin: 0 auto; 
    }
    .section { 
        background: white; 
        margin: 25px 0; 
        padding: 25px; 
        border-radius: 15px; 
        box-shadow: 0 8px 25px rgba(142, 68, 173, 0.15); 
        border: 1px solid #e6d7f5;
    }
    h1 { 
        text-align: center; 
        color: #8e44ad; 
        margin-bottom: 30px; 
        font-weight: 700;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    h2 { 
        color: #8e44ad; 
        border-bottom: 2px solid #f0e6ff; 
        padding-bottom: 12px; 
        margin-top: 0;
        font-weight: 600;
    }
    .form-group { 
        margin: 18px 0; 
    }
    label { 
        display: block; 
        font-weight: 600; 
        margin-bottom: 8px; 
        color: #2c3e50;
        font-size: 14px;
    }
    select, input, textarea { 
        width: 100%; 
        padding: 12px; 
        border: 2px solid #e6d7f5; 
        border-radius: 8px; 
        font-size: 15px;
        transition: border-color 0.3s;
    }
    select:focus, input:focus, textarea:focus { 
        outline: none; 
        border-color: #8e44ad; 
        box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
    }
    button { 
        background: #8e44ad; 
        color: white; 
        padding: 12px 20px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 15px; 
        font-weight: 600;
        margin: 8px 5px 0 0;
        transition: background 0.3s;
    }
    button:hover { 
        background: #732d91; 
    }
    button.confirmar { 
        background: #27ae60; 
    }
    button.confirmar:hover { 
        background: #219653; 
    }
    button.cancelar { 
        background: #e74c3c; 
    }
    button.cancelar:hover { 
        background: #c0392b; 
    }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 20px 0; 
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    th, td { 
        padding: 14px; 
        text-align: left; 
        border-bottom: 1px solid #f0e6ff; 
    }
    th { 
        background: #f8f4ff; 
        color: #8e44ad; 
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
    }
    tr:hover { 
        background: #faf7ff; 
    }
    .estado-pendiente { 
        color: #f39c12; 
        font-weight: bold; 
    }
    .estado-confirmada { 
        color: #27ae60; 
        font-weight: bold; 
    }
    .estado-cancelada { 
        color: #e74c3c; 
        font-weight: bold; 
        text-decoration: line-through; 
    }
    .estado-Limpia { 
        color: #27ae60; 
        font-weight: bold; 
    }
    .estado-Sucia { 
        color: #e74c3c; 
        font-weight: bold; 
    }
    .estado-En\ Limpieza { 
        color: #f39c12; 
        font-weight: bold; 
    }
    .user-header {
        text-align: center; 
        margin-bottom: 30px; 
        padding: 15px; 
        background: white; 
        border-radius: 15px; 
        box-shadow: 0 8px 25px rgba(142, 68, 173, 0.15);
        border: 1px solid #e6d7f5;
    }
    .logout-btn {
        display: inline-block; 
        padding: 10px 20px; 
        background: #e74c3c; 
        color: white; 
        text-decoration: none; 
        border-radius: 6px;
        font-weight: 600;
        margin-left: 20px;
        transition: background 0.3s;
    }
    .logout-btn:hover {
        background: #c0392b;
    }
</style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; color: #2c3e50;">🏨 Panel de Administración - SGH</h1>
        
        <!-- Información del usuario admin -->
        <div style="text-align: center; margin-bottom: 20px; padding: 10px; background: #e8f4f8; border-radius: 5px;">
            <strong>👤 Conectado como:</strong> <?= htmlspecialchars($_SESSION['nombreUsuario']) ?> (Administrador)
            <a href="../cerrar_sesion.php" style="margin-left: 20px; color: #e74c3c; text-decoration: none;">🔒 Cerrar Sesión</a>
        </div>

        <!-- GESTIÓN DE USUARIOS REGISTRADOS (todos los que pueden hacer login) -->
        <div class="section">
            <h2>👥 Usuarios Registrados (Pueden hacer login)</h2>
            <p><em>Estos usuarios están registrados en el sistema pero aún no han hecho ninguna reserva.</em></p>
            <?php if ($usuarios_registrados): ?>
                <table>
                    <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th></tr></thead>
                    <tbody>
                        <?php foreach ($usuarios_registrados as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= $u['rol'] === 'admin' ? 'Administrador' : 'Usuario' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay usuarios registrados.</p>
            <?php endif; ?>
        </div>

        <!-- GESTIÓN DE HUÉSPEDES REALES (solo quienes han reservado) -->
        <div class="section">
            <h2>🏨 Huéspedes Reales (Han hecho al menos una reserva)</h2>
            <?php if ($huespedes_reales): ?>
                <table>
                    <thead><tr><th>Nombre</th><th>Email</th><th>Documento Identidad</th></tr></thead>
                    <tbody>
                        <?php foreach ($huespedes_reales as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['nombre']) ?></td>
                                <td><?= htmlspecialchars($h['email']) ?></td>
                                <td><?= htmlspecialchars($h['documento_identidad']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay huéspedes reales (ningún usuario ha hecho una reserva aún).</p>
            <?php endif; ?>
        </div>

        <!-- GESTIÓN DE RESERVAS -->
        <div class="section">
            <h2>📅 Gestionar Reservas</h2>
            <?= $mensaje_reservas_admin ?>
            <?php if ($reservas): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Huésped</th><th>Habitación</th><th>Fechas</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['huesped']) ?></td>
                                <td><?= htmlspecialchars($r['habitacion']) ?></td>
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
                                <td>
                                    <?php if ($r['estado'] === 'Pendiente'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="accion" value="confirmar">
                                            <input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="confirmar">Confirmar</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="accion" value="cancelar">
                                            <input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
                                            <button type="submit" class="cancelar">Cancelar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- GESTIÓN DE HABITACIONES -->
        <div class="section">
            <h2>🛏️ Gestionar Habitaciones y Limpieza</h2>
            <?= $mensaje_habitaciones ?>
            <table>
                <thead><tr><th>Número</th><th>Tipo</th><th>Precio</th><th>Estado Limpieza</th><th>Acción</th></tr></thead>
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
        </div>

        <!-- GESTIÓN DE MANTENIMIENTO -->
        <div class="section">
            <h2>🔧 Registrar Tareas de Mantenimiento</h2>
            <?= $mensaje_mantenimiento ?>
            <form method="POST" style="max-width: 600px;">
                <input type="hidden" name="accion" value="crear_mantenimiento">
                <div class="form-group">
                    <label>Habitación:</label>
                    <select name="habitacion_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($habitaciones_mant as $h): ?>
                            <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['numero']) ?> (<?= $h['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de inicio:</label>
                    <input type="date" name="fecha_inicio" required>
                </div>
                <div class="form-group">
                    <label>Fecha de fin:</label>
                    <input type="date" name="fecha_fin" required>
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" required placeholder="Ej: Arreglar grifo"></textarea>
                </div>
                <button type="submit">Registrar Tarea</button>
            </form>
            <h3>Tareas registradas (<?= count($tareas_mantenimiento) ?>)</h3>
            <?php if ($tareas_mantenimiento): ?>
                <table>
                    <thead><tr><th>Habitación</th><th>Descripción</th><th>Fechas</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach ($tareas_mantenimiento as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['habitacion']) ?></td>
                                <td><?= htmlspecialchars($t['descripcion']) ?></td>
                                <td><?= $t['fecha_inicio'] ?> → <?= $t['fecha_fin'] ?></td>
                                <td><?= htmlspecialchars($t['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div style="position: fixed; top: 20px; right: 20px; background: white; padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <a href="?lang=es" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'es' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇪🇸 ES</a>
    <a href="?lang=en" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'en' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇬🇧 EN</a>
</div>
</body>
</html>