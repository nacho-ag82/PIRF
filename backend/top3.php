<?php
require_once "conexion.php";
header('Content-Type: application/json');

// Manejo global de errores para devolver siempre JSON
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno del servidor.", "details" => $e->getMessage()]);
    exit;
});

try {
    // TOP 3 fotos más votadas (más veces ganadoras)
    $stmt = $pdo->query("
        SELECT f.id, f.titulo, COUNT(v.foto_ganadora_id) AS votos
        FROM fotografias f
        LEFT JOIN votos v ON f.id = v.foto_ganadora_id
        WHERE f.estado = 'admitida'
        GROUP BY f.id
        ORDER BY votos DESC
        LIMIT 3
    ");

    $top = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($top);
    exit;
} catch (Throwable $e) {
    echo json_encode(["error" => "Error al obtener el Top 3.", "details" => $e->getMessage()]);
    exit;
}
?>
