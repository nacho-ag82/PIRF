<?php
require_once "conexion.php";
// Obtener la IP del usuario
$ip = $_SERVER['REMOTE_ADDR'];

// Consulta para contar los votos de la IP en el día actual
$sql = "SELECT COUNT(*) AS votos_hoy 
        FROM votos 
        WHERE ip = :ip AND DATE(fecha) = CURDATE()";

$stmt = $pdo->prepare($sql);
$stmt->execute(['ip' => $ip]);
$result = $stmt->fetch();

header('Content-Type: application/json');
echo json_encode(['ip' => $ip, 'votos_hoy' => $result['votos_hoy']]);