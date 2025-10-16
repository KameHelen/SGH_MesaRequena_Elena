<?php
// config.php — ¡NADA de echo, print, ni salida!

$host = 'localhost';
$db = 'sgh';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Recomendado para seguridad
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opciones);
    // ✅ Conexión establecida, pero NO se imprime nada
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}