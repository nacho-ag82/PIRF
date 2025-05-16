<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

$id = $_POST['foto_id'] ?? null;
$estado = $_POST['nuevo_estado'] ?? null;

$estadosValidos = ['pendiente', 'admitida', 'rechazada'];
if (!$id || !in_array($estado, $estadosValidos)) {
    echo json_encode(["success" => false, "message" => "Datos inválidos."]);
    exit;
}

$stmt = $pdo->prepare("UPDATE fotografias SET estado = ? WHERE id = ?");
$stmt->execute([$estado, $id]);

echo json_encode(["success" => true, "message" => "Estado actualizado."]);
?>
