<?php
session_start();
require_once "conexion.php";

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

try {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT id, titulo FROM fotografias WHERE usuario_id = ? ORDER BY fecha_subida DESC");
    $stmt->execute([$usuario_id]);
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($fotos);
} catch (Throwable $e) {
    echo json_encode([]);
}
exit;
