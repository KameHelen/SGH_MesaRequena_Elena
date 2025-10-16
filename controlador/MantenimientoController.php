<?php
// controlador/MantenimientoController.php

require_once __DIR__ . '/../modelo/TareaMantenimiento.php';

class MantenimientoController {
    private $mantenimientoModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->mantenimientoModel = new TareaMantenimiento($pdo);
    }

    public function index() {
        $mensaje = '';
        $tipo_mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $habitacion_id = (int)$_POST['habitacion_id'];
                $descripcion = trim($_POST['descripcion'] ?? '');
                $fecha_inicio = $_POST['fecha_inicio'] ?? '';
                $fecha_fin = $_POST['fecha_fin'] ?? '';

                if (empty($descripcion)) {
                    throw new Exception("La descripción es obligatoria.");
                }
                if (empty($fecha_inicio) || empty($fecha_fin)) {
                    throw new Exception("Las fechas son obligatorias.");
                }
                if ($fecha_fin < $fecha_inicio) {
                    throw new Exception("La fecha de fin debe ser posterior o igual a la de inicio.");
                }

                $this->mantenimientoModel->crear($habitacion_id, $descripcion, $fecha_inicio, $fecha_fin);
                $mensaje = "✅ Tarea de mantenimiento registrada correctamente.";
                $tipo_mensaje = 'exito';

            } catch (Exception $e) {
                $mensaje = "❌ " . htmlspecialchars($e->getMessage());
                $tipo_mensaje = 'error';
            }
        }

        $habitaciones = $this->mantenimientoModel->obtenerHabitaciones();
        $tareas = $this->mantenimientoModel->obtenerTodas();
        include __DIR__ . '/../vista/admin/mantenimiento.php';
    }
}
?>