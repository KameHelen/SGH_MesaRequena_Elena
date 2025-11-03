<?php
// idioma.php

// Establecer idioma por defecto
$idioma = 'es';

// Si se envía un nuevo idioma por POST/GET, actualizar la cookie
if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], ['es', 'en'])) {
    $idioma = $_REQUEST['lang'];
    // Cookie que dura 30 días
    setcookie('idioma', $idioma, time() + (86400 * 30), '/', '', false, true);
}

// Si existe la cookie de idioma, usarla
if (isset($_COOKIE['idioma']) && in_array($_COOKIE['idioma'], ['es', 'en'])) {
    $idioma = $_COOKIE['idioma'];
}

// Textos en ambos idiomas
$textos = [
    'es' => [
        'hotel_nombre' => 'Hotel El Gran Descanso',
        'login_titulo' => '🔒 Iniciar Sesión',
        'registro_titulo' => '📝 Crear Cuenta',
        'email' => 'Email',
        'contrasena' => 'Contraseña',
        'nombre_completo' => 'Nombre completo',
        'confirmar_contrasena' => 'Confirmar contraseña',
        'iniciar_sesion' => 'Iniciar Sesión',
        'crear_cuenta' => 'Crear Cuenta',
        'reserva_titulo' => '➕ Nueva Reserva',
        'habitacion' => 'Habitación',
        'fecha_llegada' => 'Fecha de llegada',
        'fecha_salida' => 'Fecha de salida',
        'documento_identidad' => 'Documento de Identidad',
        'crear_reserva' => 'Crear Reserva',
        'bienvenido' => 'Bienvenido',
        'usuario' => 'Usuario',
        'administrador' => 'Administrador',
        'cerrar_sesion' => 'Cerrar Sesión',
        'huéspedes_reales' => '🏨 Huéspedes Reales (Han hecho al menos una reserva)',
        'usuarios_registrados' => '👥 Usuarios Registrados (Pueden hacer login)',
        'gestion_reservas' => '📅 Gestionar Reservas',
        'gestion_habitaciones' => '🛏️ Gestionar Habitaciones y Limpieza',
        'gestion_mantenimiento' => '🔧 Registrar Tareas de Mantenimiento',
        'no_huespedes' => 'No hay huéspedes reales (ningún usuario ha hecho una reserva aún).',
        'no_usuarios' => 'No hay usuarios registrados.',
        'documento_obligatorio' => 'El documento de identidad es obligatorio para tu primera reserva.',
        'reserva_exito' => '✅ Reserva creada con éxito.',
        'email_contrasena_incorrectos' => 'Email o contraseña incorrectos.',
        'ya_tienes_cuenta' => '¿Ya tienes cuenta?',
        'no_tienes_cuenta' => '¿No tienes cuenta?',
        'registrar_aqui' => 'Regístrate aquí',
        'iniciar_aqui' => 'Iniciar sesión',
        'credenciales_prueba' => 'Credenciales de prueba:'
    ],
    'en' => [
        'hotel_nombre' => 'The Great Rest Hotel',
        'login_titulo' => '🔒 Login',
        'registro_titulo' => '📝 Create Account',
        'email' => 'Email',
        'contrasena' => 'Password',
        'nombre_completo' => 'Full Name',
        'confirmar_contrasena' => 'Confirm Password',
        'iniciar_sesion' => 'Login',
        'crear_cuenta' => 'Create Account',
        'reserva_titulo' => '➕ New Reservation',
        'habitacion' => 'Room',
        'fecha_llegada' => 'Check-in Date',
        'fecha_salida' => 'Check-out Date',
        'documento_identidad' => 'ID Document',
        'crear_reserva' => 'Create Reservation',
        'bienvenido' => 'Welcome',
        'usuario' => 'User',
        'administrador' => 'Administrator',
        'cerrar_sesion' => 'Logout',
        'huéspedes_reales' => '🏨 Real Guests (Have made at least one reservation)',
        'usuarios_registrados' => '👥 Registered Users (Can login)',
        'gestion_reservas' => '📅 Manage Reservations',
        'gestion_habitaciones' => '🛏️ Manage Rooms and Cleaning',
        'gestion_mantenimiento' => '🔧 Register Maintenance Tasks',
        'no_huespedes' => 'No real guests yet (no user has made a reservation).',
        'no_usuarios' => 'No registered users.',
        'documento_obligatorio' => 'ID document is required for your first reservation.',
        'reserva_exito' => '✅ Reservation created successfully.',
        'email_contrasena_incorrectos' => 'Incorrect email or password.',
        'ya_tienes_cuenta' => 'Already have an account?',
        'no_tienes_cuenta' => 'Don\'t have an account?',
        'registrar_aqui' => 'Register here',
        'iniciar_aqui' => 'Login',
        'credenciales_prueba' => 'Test credentials:'
    ]
];

// Función para obtener texto traducido
function t($clave) {
    global $textos, $idioma;
    return $textos[$idioma][$clave] ?? $clave;
}
?>