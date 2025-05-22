<?php
require_once "conexion.php";
header('Content-Type: application/json');

// Limpia cualquier salida previa
if (ob_get_level()) ob_end_clean();

try {
    $query = "
        SELECT f.id, f.titulo, u.nombre AS autor,
               (SELECT COUNT(*) FROM votos v WHERE v.foto_ganadora_id = f.id) AS votos
        FROM fotografias f
        JOIN usuarios u ON f.usuario_id = u.id
        WHERE f.estado = 'admitida'
        ORDER BY votos DESC
    ";
    $stmt = $pdo->query($query);
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Asegúrate de que solo se imprime el JSON
    echo json_encode($fotos);
    exit;
} catch (Throwable $e) {
    echo json_encode([]);
    exit;
}

