<?php
require_once "conexion.php";

// TOP 3 fotos más votadas (más veces ganadoras)
$stmt = $pdo->query("
    SELECT f.id, COUNT(v.id) AS votos
    FROM votos v
    JOIN fotografias f ON v.foto_ganadora_id = f.id
    WHERE f.estado = 'admitida'
    GROUP BY v.foto_ganadora_id
    ORDER BY votos DESC
    LIMIT 3
");

$top = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($top);
?>
