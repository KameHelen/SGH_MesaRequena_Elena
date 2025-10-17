<?php
// modelo/Huesped.php

class Huesped {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener todos los huéspedes
    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT id, nombre, email, documento_identidad FROM huespedes ORDER BY nombre");
        return $stmt->fetchAll();
    }

    // Registrar un nuevo huésped
    public function crear($nombre, $email, $documento) {
        // Validar que el email no exista
        $stmt = $this->pdo->prepare("SELECT id FROM huespedes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Ya existe un huésped con ese email.");
        }

        $sql = "INSERT INTO huespedes (nombre, email, documento_identidad) VALUES (?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([$nombre, $email, $documento]);
    }
}
?>