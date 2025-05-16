<?php
require_once "conexion.php";
session_start();

// Verifica que el usuario sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

header('Content-Type: application/json');

try {
    // No selecciones el campo 'archivo'
    $stmt = $pdo->query("SELECT id, usuario_id, titulo, estado, fecha_subida FROM fotografias WHERE estado = 'pendiente'");
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Añade el nombre del autor si lo necesitas
    foreach ($fotos as &$foto) {
        $userStmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
        $userStmt->execute([$foto['usuario_id']]);
        $foto['autor'] = $userStmt->fetchColumn();
    }

    echo json_encode($fotos);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error al obtener fotos.", "error" => $e->getMessage()]);
}
?>
