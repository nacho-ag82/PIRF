<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "conexion.php";
require_once "es_fecha_valida.php";

header('Content-Type: application/json');
// Obtener la fecha de fin de votación desde la configuración
$stmt = $pdo->query("SELECT lim_fvoto FROM configuracion LIMIT 1");
$config = $stmt->fetch(PDO::FETCH_ASSOC);
$lim_fvoto = $config['lim_fvoto'] ?? null;

if ($lim_fvoto && date('Y-m-d') > $lim_fvoto) {
    echo json_encode(["success" => false, "message" => "El plazo de votación ha terminado."]);
    exit;
}

$fotoGanadora = isset($_POST['ganadora']) ? (int)$_POST['ganadora'] : null;
$fotoPerdedora = isset($_POST['perdedora']) ? (int)$_POST['perdedora'] : null;
$ip = $_SERVER['REMOTE_ADDR'];

if (!$fotoGanadora || !$fotoPerdedora || $fotoGanadora === $fotoPerdedora) {
    echo json_encode(["success" => false, "message" => "Datos inválidos"]);
    exit;
}

// (opcional) Límite de votos diarios por IP
$maxVotosPorDia = 10;
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM votos WHERE ip = ? AND DATE(fecha) = CURDATE()");
$stmt->execute([$ip]);
$totalVotosHoy = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
if ($totalVotosHoy >= $maxVotosPorDia) {
    echo json_encode(["success" => false, "message" => "Máximo de votos permitidos alcanzado para hoy."]);
    exit;
}

// Verifica si ya existe un voto para esta combinación hoy desde esta IP (en cualquier orden)
$stmt = $pdo->prepare("
    SELECT 1 FROM votos
    WHERE 
        (
            (foto_ganadora_id = ? AND foto_perdedora_id = ?)
            OR
            (foto_ganadora_id = ? AND foto_perdedora_id = ?)
        )
        AND ip = ?
        AND DATE(fecha) = CURDATE()
");
$stmt->execute([$fotoGanadora, $fotoPerdedora, $fotoPerdedora, $fotoGanadora, $ip]);
if ($stmt->fetch()) {
    echo json_encode(["success" => false, "message" => "Ya has votado este duelo hoy."]);
    exit;
}

// Inserta el voto
$stmt = $pdo->prepare("
    INSERT INTO votos (foto_ganadora_id, foto_perdedora_id, ip, fecha)
    VALUES (?, ?, ?, NOW())
");
try {
    $stmt->execute([$fotoGanadora, $fotoPerdedora, $ip]);
    echo json_encode(["success" => true, "message" => "Voto registrado"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error al registrar voto"]);
}