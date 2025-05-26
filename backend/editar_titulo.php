<?php
session_start();
require_once "conexion.php";

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

$id = $_POST['id'] ?? null;
$titulo = $_POST['titulo'] ?? null;

if (!$id || !$titulo) {
    echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    exit;
}

try {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("UPDATE fotografias SET titulo = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $id, $usuario_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Título actualizado."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo actualizar el título."]);
    }
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Error interno."]);
}
exit;
