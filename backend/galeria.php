<?php
require_once "conexion.php";
header('Content-Type: application/json');

try {
    // Top 3 fotos más votadas
    $queryTop3 = "
        SELECT f.id, f.titulo, f.archivo, u.nombre AS autor,
               (SELECT COUNT(*) FROM votos v WHERE v.foto_ganadora_id = f.id) AS votos
        FROM fotografias f
        JOIN usuarios u ON f.usuario_id = u.id
        WHERE f.estado = 'admitida'
        GROUP BY f.id
        ORDER BY votos DESC
        LIMIT 3
    ";
    $stmtTop3 = $pdo->query($queryTop3);
    $top3 = $stmtTop3->fetchAll(PDO::FETCH_ASSOC);

    // IDs de las top 3 para excluirlas del resto
    $top3_ids = array_column($top3, 'id');
    $placeholders = implode(',', array_fill(0, count($top3_ids), '?'));

    // Resto de fotos admitidas (excluyendo top 3)
    $queryFotos = "
        SELECT f.id, f.titulo, f.archivo, u.nombre AS autor,
               (SELECT COUNT(*) FROM votos v WHERE v.foto_ganadora_id = f.id) AS votos
        FROM fotografias f
        JOIN usuarios u ON f.usuario_id = u.id
        WHERE f.estado = 'admitida'
        " . (count($top3_ids) ? "AND f.id NOT IN ($placeholders)" : "") . "
        ORDER BY f.fecha_subida DESC
    ";
    $stmtFotos = $pdo->prepare($queryFotos);
    $stmtFotos->execute($top3_ids);
    $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'top3' => $top3,
        'fotos' => $fotos
    ]);
} catch (Exception $e) {
    echo json_encode(['top3' => [], 'fotos' => []]);
}
?>
