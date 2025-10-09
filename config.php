<?php

$host = 'localhost';
$db = 'sgh';
$user = 'root';
$pass = '';         
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
];
try {
    $pdo = new PDO($dsn,$user, $pass, $opciones);  
    echo "Conexión exitosa";

    /*if($stmt->execute()){
        echo "Conexión exitosa";
    } else {
        echo "Error en la ejecución de la consulta";
    }*/
} catch (PDOException $e) {
    die("Error de conexion a la base de datos: " . $e->getMessage());
}