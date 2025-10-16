<?php
// controlador/ReservaAdminController.php

require_once __DIR__ . '/../modelo/Reserva.php';

class ReservaAdminController {
    private $reservaModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservaModel = new Reserva($pdo);
    }

    public function index() {
        $mensaje = '';
        $tipo_mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
            try {
                $reserva_id = (int)$_POST['reserva_id'];
                $accion = $_POST['accion'];
                $estado = ($accion === 'confirmar') ? 'Confirmada' : 'Cancelada';

                $this->reservaModel->actualizarEstado($reserva_id, $estado);
                $mensaje = "✅ Reserva #$reserva_id $accion correctamente.";
                $tipo_mensaje = 'exito';

            } catch (Exception $e) {
                $mensaje = "❌ " . htmlspecialchars($e->getMessage());
                $tipo_mensaje = 'error';
            }
        }

        $reservas = $this->reservaModel->obtenerTodas();
        include __DIR__ . '/../vista/admin/reservas.php';
    }
}
?>