<?php
require_once "conexion.php";
session_start();

header('Content-Type: application/json');

// Verifica que el usuario sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, nombre, email FROM usuarios ORDER BY nombre ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error al obtener usuarios.", "error" => $e->getMessage()]);
}
exit;
