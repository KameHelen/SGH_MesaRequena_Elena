<?php
// controlador/HuespedController.php

require_once __DIR__ . '/../modelo/Huesped.php';

class HuespedController {
    private $huespedModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->huespedModel = new Huesped($pdo);
    }

    // Manejar la vista principal
    public function index() {
        $mensaje = '';
        $tipo_mensaje = '';
        $huespedes = $this->huespedModel->obtenerTodos();

        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $documento = trim($_POST['documento'] ?? '');

                if (empty($nombre) || empty($email) || empty($documento)) {
                    throw new Exception("Todos los campos son obligatorios.");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El email no es válido.");
                }

                $this->huespedModel->crear($nombre, $email, $documento);
                $mensaje = "✅ Huésped registrado correctamente.";
                $tipo_mensaje = 'exito';

                // Recargar lista (por si hay nuevo huésped)
                $huespedes = $this->huespedModel->obtenerTodos();

            } catch (Exception $e) {
                $mensaje = "❌ " . htmlspecialchars($e->getMessage());
                $tipo_mensaje = 'error';
            }
        }

        // Pasar datos a la vista
        include __DIR__ . '/../vista/admin/huespedes.php';
    }
}
?>