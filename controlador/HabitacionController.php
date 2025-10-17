<?php
class HabitacionController {
    private $habitacionModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->habitacionModel = new Habitacion($pdo);
    }

    public function index() {
        $mensaje = '';
        $tipo_mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_limpieza') {
            try {
                $habitacion_id = (int)$_POST['habitacion_id'];
                $nuevo_estado = $_POST['estado_limpieza'];

                $this->habitacionModel->actualizarEstadoLimpieza($habitacion_id, $nuevo_estado);
                $mensaje = "Estado de limpieza actualizado correctamente.";
                $tipo_mensaje = 'exito';

            } catch (Exception $e) {
                $mensaje = " X " . htmlspecialchars($e->getMessage());
                $tipo_mensaje = 'error';
            }
        }

        $habitaciones = $this->habitacionModel->obtenerTodas();
        include __DIR__ . '/../vista/habitaciones.php';
    }
}
?>