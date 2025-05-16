<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "conexion.php";
require_once "es_fecha_valida.php";

header('Content-Type: application/json');

if (!estaPermitido('fin_votacion')) {
    echo json_encode(["success" => false, "message" => "El plazo de votación ha terminado."]);
    exit;
}

$fotoGanadora = $_POST['ganadora'] ?? null;
$fotoPerdedora = $_POST['perdedora'] ?? null;

if (!$fotoGanadora || !$fotoPerdedora) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Límite de votos por día
$configStmt = $pdo->prepare("SELECT lim_votos FROM configuracion");
$configStmt->execute();
$limite = (int) $configStmt->fetchColumn();



// Guardar voto con manejo de errores
try {
    $stmt = $pdo->prepare("INSERT INTO votos (foto_ganadora_id, foto_perdedora_id, ip, id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fotoGanadora, $fotoPerdedora, $ip, $usuario_id]);
    echo json_encode(["success" => true, "message" => "¡Voto registrado!"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al registrar voto: " . $e->getMessage()]);
}