<?php
// controlador/ReservaPublicaController.php

require_once __DIR__ . '/../modelo/Reserva.php';

class ReservaPublicaController {
    private $reservaModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservaModel = new Reserva($pdo);
    }

    public function crear() {
        $mensaje = '';
        $tipo_mensaje = '';
        $huespedes = $this->reservaModel->obtenerHuespedes();
        $habitaciones = $this->reservaModel->obtenerHabitaciones();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $huesped_id = (int)$_POST['huesped_id'];
                $habitacion_id = (int)$_POST['habitacion_id'];
                $fecha_llegada = $_POST['fecha_llegada'];
                $fecha_salida = $_POST['fecha_salida'];
                $precio_base = (float)$_POST['precio_base'];

                if ($fecha_salida <= $fecha_llegada) {
                    throw new Exception("La fecha de salida debe ser posterior a la de llegada.");
                }

                $this->reservaModel->crear($huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $precio_base);
                $mensaje = "✅ Reserva creada con éxito. Estado: Pendiente.";
                $tipo_mensaje = 'exito';

            } catch (Exception $e) {
                $mensaje = "❌ " . htmlspecialchars($e->getMessage());
                $tipo_mensaje = 'error';
            }
        }

        include __DIR__ . '/../vista/publica/reservar.php';
    }
}
?>