<?php
// login.php
session_start();

if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header("Location: bienvenida.php");
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/idioma.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $mensaje = t('email_contrasena_incorrectos');
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $_SESSION['autenticado'] = true;
            $_SESSION['nombreUsuario'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            header("Location: bienvenida.php");
            exit;
        } else {
            $mensaje = t('email_contrasena_incorrectos');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $idioma ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('login_titulo') ?> - <?= t('hotel_nombre') ?></title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            position: relative;
        }
        .login-container { 
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
            border-color: #8e44ad; 
            box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.2);
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: #8e44ad; 
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
            background: #732d91; 
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
            color: #27ae60; 
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
            background: #8e44ad;
            color: white !important;
        }
        .idioma-btn:not(.active) {
            color: #666;
        }
        .idioma-btn:not(.active):hover {
            color: #8e44ad;
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

    <div class="login-container">
        <h2><?= t('login_titulo') ?></h2>
        
        <?php if ($mensaje): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email"><?= t('email') ?>:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><?= t('contrasena') ?>:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit"><?= t('iniciar_sesion') ?></button>
        </form>
        
        <div class="login-link">
            <p><?= t('no_tienes_cuenta') ?> <a href="registro.php"><?= t('registrar_aqui') ?></a></p>
        </div>
        
        <p style="margin-top: 20px; font-size: 13px; color: #666;">
            <strong><?= t('credenciales_prueba') ?></strong><br>
            Admin: admin@hotel.com / admin123<br>
            User: user@hotel.com / user123
        </p>
    </div>
</body>
</html>