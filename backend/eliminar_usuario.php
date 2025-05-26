<?php
require_once "conexion.php";
session_start();

header('Content-Type: application/json');

// Verifica que el usuario sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(["success" => false, "message" => "ID de usuario no proporcionado."]);
    exit;
}

try {
    // Inicia una transacción para garantizar la consistencia
    $pdo->beginTransaction();

    // Obtiene las IDs de las fotos del usuario
    $stmtFotos = $pdo->prepare("SELECT id FROM fotografias WHERE usuario_id = ?");
    $stmtFotos->execute([$id]);
    $fotos = $stmtFotos->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($fotos)) {
        // Elimina los votos asociados a las fotos del usuario
        $stmtVotos = $pdo->prepare("DELETE FROM votos WHERE foto_ganadora_id IN (" . implode(',', array_fill(0, count($fotos), '?')) . ") OR foto_perdedora_id IN (" . implode(',', array_fill(0, count($fotos), '?')) . ")");
        $stmtVotos->execute(array_merge($fotos, $fotos));

        // Elimina las fotografías del usuario
        $stmtEliminarFotos = $pdo->prepare("DELETE FROM fotografias WHERE usuario_id = ?");
        $stmtEliminarFotos->execute([$id]);
    }

    // Elimina el usuario
    $stmtUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmtUsuario->execute([$id]);

    if ($stmtUsuario->rowCount() > 0) {
        $pdo->commit();
        echo json_encode(["success" => true, "message" => "Usuario, sus votos y fotografías eliminados correctamente."]);
    } else {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "No se pudo eliminar el usuario."]);
    }
} catch (Throwable $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => "Error interno.", "error" => $e->getMessage()]);
}
exit;
