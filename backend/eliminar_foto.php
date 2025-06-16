<?php
session_start();
require_once "conexion.php";

// Si no está logueado, redirige a login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
if (!$id) {
    header("Location: participante.php?error=ID de foto no especificado");
    exit;
}

try {
    // Solo permite borrar si es dueño de la foto
    $usuario_id = $_SESSION['usuario_id'];

    // Iniciar transacción
    $pdo->beginTransaction();

    // Eliminar votos relacionados con la foto
    $stmt_votos = $pdo->prepare("DELETE FROM votos WHERE foto_ganadora_id = ? OR foto_perdedora_id = ?");
    $stmt_votos->execute([$id, $id]);

    // Eliminar la foto
    $stmt = $pdo->prepare("DELETE FROM fotografias WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo json_encode(["success" => true, "message" => "fotografia eliminada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar la foto."]);
    }
    exit;
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error al eliminar la foto."]);
    exit;
}
