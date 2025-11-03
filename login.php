<?php
// login.php
session_start();

if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header("Location: bienvenida.php");
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/idioma.php'; // ✅ Incluir idioma

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
        }
        .login-container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 50px rgba(0,0,0,0.3); 
            width: 380px; 
            text-align: center;
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
        .idioma-selector {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .idioma-btn {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 5px;
        }
        .idioma-btn.active {
            background: #8e44ad;
            color: white;
            border-color: #8e44ad;
        }
    </style>
</head>
<body>
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
        
        <div class="idioma-selector">
            <strong>Language / Idioma:</strong><br>
            <a href="?lang=es" class="idioma-btn <?= $idioma === 'es' ? 'active' : '' ?>">🇪🇸 ES</a>
            <a href="?lang=en" class="idioma-btn <?= $idioma === 'en' ? 'active' : '' ?>">🇬🇧 EN</a>
        </div>
        
        <p style="margin-top: 20px; text-align: center; font-size: 14px;">
            <?= t('no_tienes_cuenta') ?> <a href="registro.php" style="color: #27ae60; text-decoration: none;"><?= t('registrar_aqui') ?></a>
        </p>
        
        <p style="margin-top: 15px; font-size: 13px; color: #666;">
            <strong><?= t('credenciales_prueba') ?></strong><br>
            Admin: admin@hotel.com / admin123<br>
            User: user@hotel.com / user123
        </p>
    </div>
    <div style="position: fixed; top: 20px; right: 20px; background: white; padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <a href="?lang=es" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'es' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇪🇸 ES</a>
    <a href="?lang=en" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'en' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇬🇧 EN</a>
</div>
</body>
</html>