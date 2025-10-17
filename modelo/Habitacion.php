<?php

class Habitacion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todas las habitaciones
    public function obtenerTodas() {
        $stmt = $this->pdo->query("SELECT id, numero, tipo, precio_base, estado_limpieza FROM habitaciones ORDER BY numero");
        return $stmt->fetchAll();
    }

    // Actualizar estado de limpieza
    public function actualizarEstadoLimpieza($id, $estado) {
        $estados_validos = ['Limpia', 'Sucia', 'En Limpieza'];
        if (!in_array($estado, $estados_validos)) {
            throw new Exception("Estado de limpieza no válido.");
        }

        $sql = "UPDATE habitaciones SET estado_limpieza = ? WHERE id = ?";
        return $this->pdo->prepare($sql)->execute([$estado, $id]);
    }
}
?>