<?php
require_once "conexion.php";
header('Content-Type: application/json');

try {
    // Fotos más votadas (más veces como ganadoras)
    $stmt = $pdo->query("
        SELECT f.titulo, COUNT(v.foto_ganadora_id) AS votos
        FROM votos v
        JOIN fotografias f ON v.foto_ganadora_id = f.id
        WHERE f.estado = 'admitida'
        GROUP BY f.id
        ORDER BY votos DESC
        LIMIT 5
    ");
    $fotosTop = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Participantes con más fotos subidas
    $stmt = $pdo->query("
        SELECT u.nombre, COUNT(f.id) AS total_fotos
        FROM usuarios u
        JOIN fotografias f ON u.id = f.usuario_id
        WHERE f.estado = 'admitida'
        GROUP BY u.id
        ORDER BY total_fotos DESC
        LIMIT 5
    ");
    $usuariosTop = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Votos por día (últimos 7 días)
    $stmt = $pdo->query("
        SELECT DATE(v.fecha) AS fecha, COUNT(*) AS cantidad
        FROM votos v
        WHERE v.fecha >= CURDATE() - INTERVAL 7 DAY
        GROUP BY DATE(v.fecha)
        ORDER BY fecha
    ");
    $votosPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "fotosTop" => $fotosTop,
        "usuariosTop" => $usuariosTop,
        "votosPorDia" => $votosPorDia
    ]);
    exit;
} catch (Throwable $e) {
    echo json_encode(["error" => "Error al obtener estadísticas.", "details" => $e->getMessage()]);
    exit;
}
