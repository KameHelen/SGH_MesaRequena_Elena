<?php

class TareaMantenimiento {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Registrar nueva tarea
    public function crear($habitacion_id, $descripcion, $fecha_inicio, $fecha_fin) {
        // Validar que la habitación exista
        $stmt = $this->pdo->prepare("SELECT id FROM habitaciones WHERE id = ?");
        $stmt->execute([$habitacion_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Habitación no válida.");
        }

        $sql = "
            INSERT INTO tareas_mantenimiento (habitacion_id, descripcion, fecha_inicio, fecha_fin, estado)
            VALUES (?, ?, ?, ?, 'Activa')
        ";
        return $this->pdo->prepare($sql)->execute([$habitacion_id, $descripcion, $fecha_inicio, $fecha_fin]);
    }

    // Obtener todas las tareas con datos de habitación
    public function obtenerTodas() {
        $sql = "
            SELECT 
                tm.id,
                tm.descripcion,
                tm.fecha_inicio,
                tm.fecha_fin,
                tm.estado,
                h.numero AS habitacion_numero,
                h.tipo AS habitacion_tipo
            FROM tareas_mantenimiento tm
            JOIN habitaciones h ON tm.habitacion_id = h.id
            ORDER BY tm.fecha_inicio DESC
        ";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Obtener habitaciones para el formulario
    public function obtenerHabitaciones() {
        return $this->pdo->query("SELECT id, numero, tipo FROM habitaciones ORDER BY numero")->fetchAll();
    }
}
?>