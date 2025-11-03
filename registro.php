<?php
// registro.php
session_start();
require_once __DIR__ . '/../idioma.php'; 
// Si ya está autenticado, redirigir
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header("Location: bienvenida.php");
    exit;
}

$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/config.php';
    
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El email no es válido.";
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $password_confirm) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mensaje = "Ya existe una cuenta con ese email.";
        } else {
            // Registrar nuevo usuario (rol = 'usuario')
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')";
            $pdo->prepare($sql)->execute([$nombre, $email, $password]);
            
            // Iniciar sesión automáticamente
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - SGH</title>
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
    .login-link a { 
        color: #8e44ad; 
        text-decoration: none; 
        font-weight: 600;
        display: inline-block;
        margin-top: 15px;
    }
    .login-link a:hover { 
        text-decoration: underline; 
    }
</style>
</head>
<body>
    <div class="register-container">
        <h2>📝 Crear Cuenta</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmar contraseña:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit">Crear Cuenta</button>
        </form>
        
        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
        </div>
    </div>
    <div style="position: fixed; top: 20px; right: 20px; background: white; padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <a href="?lang=es" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'es' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇪🇸 ES</a>
    <a href="?lang=en" style="text-decoration: none; margin: 0 5px; <?= $idioma === 'en' ? 'font-weight: bold; color: #8e44ad;' : '' ?>">🇬🇧 EN</a>
</div>
</body>
</html>