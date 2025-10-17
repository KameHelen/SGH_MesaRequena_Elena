<?php

class Reserva {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Verificar disponibilidad de habitación en fechas dadas
    public function habitacionDisponible($habitacion_id, $fecha_llegada, $fecha_salida) {
    
        $sql1 = "
            SELECT COUNT(*) 
            FROM reservas 
            WHERE habitacion_id = ? 
            AND estado = 'Confirmada'
            AND fecha_llegada < ? 
            AND fecha_salida > ?
        ";
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt1->fetchColumn() > 0) {
            return false;
        }

        $sql2 = "
            SELECT COUNT(*) 
            FROM tareas_mantenimiento 
            WHERE habitacion_id = ? 
            AND estado = 'Activa'
            AND fecha_inicio <= ? 
            AND fecha_fin >= ?
        ";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([$habitacion_id, $fecha_salida, $fecha_llegada]);
        if ($stmt2->fetchColumn() > 0) {
            return false;
        }

        return true;
    }

    // Crear nueva reserva (Pendiente)
    public function crear($huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $precio_base) {
        if (!$this->habitacionDisponible($habitacion_id, $fecha_llegada, $fecha_salida)) {
            throw new Exception("La habitación no está disponible en esas fechas.");
        }

        $dias = (strtotime($fecha_salida) - strtotime($fecha_llegada)) / 86400;
        $precio_total = $dias * $precio_base;

        $sql = "
            INSERT INTO reservas (huesped_id, habitacion_id, fecha_llegada, fecha_salida, precio_total, estado)
            VALUES (?, ?, ?, ?, ?, 'Pendiente')
        ";
        return $this->pdo->prepare($sql)->execute([$huesped_id, $habitacion_id, $fecha_llegada, $fecha_salida, $precio_total]);
    }

    // Confirmar o cancelar una reserva
    public function actualizarEstado($reserva_id, $nuevo_estado) {
        $estados_validos = ['Confirmada', 'Cancelada'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            throw new Exception("Estado no válido.");
        }

        $sql = "UPDATE reservas SET estado = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$nuevo_estado, $reserva_id]);
    }

    // Obtener todas las reservas
    public function obtenerTodas() {
        $sql = "
            SELECT 
                r.id,
                r.fecha_llegada,
                r.fecha_salida,
                r.precio_total,
                r.estado,
                r.fecha_reserva,
                h.nombre AS huesped,
                hab.numero AS habitacion_numero,
                hab.tipo AS habitacion_tipo
            FROM reservas r
            JOIN huespedes h ON r.huesped_id = h.id
            JOIN habitaciones hab ON r.habitacion_id = hab.id
            ORDER BY r.fecha_reserva DESC
        ";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Obtener listas para formularios
    public function obtenerHuespedes() {
        return $this->pdo->query("SELECT id, nombre FROM huespedes")->fetchAll();
    }

    public function obtenerHabitaciones() {
        return $this->pdo->query("SELECT id, numero, tipo, precio_base FROM habitaciones")->fetchAll();
    }
}
?>