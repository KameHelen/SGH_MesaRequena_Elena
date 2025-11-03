<?php
// registro.php
session_start();
require_once __DIR__ . '/../idioma.php'; 

if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header("Location: bienvenida.php");
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/idioma.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nombre) || empty($email) || empty($password)) {
        $mensaje = t('todos_campos_obligatorios');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = t('email_no_valido');
    } elseif (strlen($password) < 6) {
        $mensaje = t('contrasena_min_6');
    } elseif ($password !== $password_confirm) {
        $mensaje = t('contrasenas_no_coinciden');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mensaje = t('email_ya_existe');
        } else {
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')";
            $pdo->prepare($sql)->execute([$nombre, $email, $password]);
            $_SESSION['autenticado'] = true;
            $_SESSION['nombreUsuario'] = $nombre;
            $_SESSION['email'] = $email;
            $_SESSION['rol'] = 'usuario';
            header("Location: bienvenida.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('registro_titulo') ?> - <?= t('hotel_nombre') ?></title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            position: relative;
        }
        .register-container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 50px rgba(0,0,0,0.3); 
            width: 380px; 
            text-align: center;
            z-index: 10;
        }
        h2 { 
            color: #8e44ad; 
            margin-bottom: 25px; 
            font-weight: 700;
        }
        .form-group { 
            margin-bottom: 20px; 
            text-align: left;
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #2c3e50;
            font-size: 14px;
        }
        input { 
            width: 100%; 
            padding: 14px; 
            border: 2px solid #e6d7f5; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus { 
            outline: none; 
            border-color: #9b59b6; 
            box-shadow: 0 0 0 3px rgba(155, 89, 182, 0.2);
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: #9b59b6; 
            color: white; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            margin-top: 10px;
            transition: background 0.3s;
        }
        button:hover { 
            background: #8e44ad; 
        }
        .alert { 
            padding: 12px; 
            margin-bottom: 20px; 
            border-radius: 10px; 
            background: #ffebee; 
            color: #e74c3c; 
            border: 1px solid #ffcdd2;
            font-weight: 500;
        }
        .login-link { 
            margin-top: 20px; 
            text-align: center; 
        }
        .login-link a { 
            color: #3498db; 
            text-decoration: none; 
            font-weight: 600;
        }
        .login-link a:hover { 
            text-decoration: underline; 
        }
        
        /* Selector de idioma en la esquina superior derecha */
        .idioma-selector {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 100;
        }
        .idioma-btn {
            text-decoration: none;
            padding: 5px 10px;
            margin: 0 3px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        .idioma-btn.active {
            background: #9b59b6;
            color: white !important;
        }
        .idioma-btn:not(.active) {
            color: #666;
        }
        .idioma-btn:not(.active):hover {
            color: #9b59b6;
            background: #f8f4ff;
        }
    </style>
</head>
<body>
    <!-- Selector de idioma en la esquina superior derecha -->
    <div class="idioma-selector">
        <a href="?lang=es" class="idioma-btn <?= $idioma === 'es' ? 'active' : '' ?>">🇪🇸 ES</a>
        <a href="?lang=en" class="idioma-btn <?= $idioma === 'en' ? 'active' : '' ?>">🇬🇧 EN</a>
    </div>

    <div class="register-container">
        <h2><?= t('registro_titulo') ?></h2>
        
        <?php if ($mensaje): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nombre"><?= t('nombre_completo') ?>:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email"><?= t('email') ?>:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password"><?= t('contrasena') ?>:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm"><?= t('confirmar_contrasena') ?>:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit"><?= t('crear_cuenta') ?></button>
        </form>
        
        <div class="login-link">
            <p><?= t('ya_tienes_cuenta') ?> <a href="login.php"><?= t('iniciar_aqui') ?></a></p>
        </div>
    </div>
</body>
</html>