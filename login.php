<?php
// login.php
session_start();

// Si ya está autenticado, redirigir
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header("Location: bienvenida.php");
    exit;
}

require_once __DIR__ . '/config.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $mensaje = "Por favor, complete todos los campos.";
    } else {
        // Buscar usuario por email
        $stmt = $pdo->prepare("SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Login exitoso
            $_SESSION['autenticado'] = true;
            $_SESSION['nombreUsuario'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: bienvenida.php");
            exit;
        } else {
            $mensaje = "Email o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - SGH</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2980b9; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔒 Iniciar Sesión</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>