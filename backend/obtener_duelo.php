<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "conexion.php";

$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'];

// Obtener IDs de fotos ya votadas hoy por el usuario (como ganadora o perdedora)
if ($usuario_id) {
    $votosStmt = $pdo->prepare("
        SELECT foto_ganadora_id, foto_perdedora_id
        FROM votos
        WHERE id = ? AND DATE(fecha) = CURDATE()
    ");
    $votosStmt->execute([$usuario_id]);
} else {
    $votosStmt = $pdo->prepare("
        SELECT foto_ganadora_id, foto_perdedora_id
        FROM votos
        WHERE ip = ? AND DATE(fecha) = CURDATE()
    ");
    $votosStmt->execute([$ip]);
}
$votadas = [];
foreach ($votosStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $votadas[] = $row['foto_ganadora_id'];
    $votadas[] = $row['foto_perdedora_id'];
}
$votadas = array_unique($votadas);

// Construir la cláusula para excluir fotos ya votadas hoy
$excluir = "";
$params = [];
if (!empty($votadas)) {
    $placeholders = implode(',', array_fill(0, count($votadas), '?'));
    $excluir = "AND id NOT IN ($placeholders)";
    $params = array_merge($params, $votadas);
}

// Excluir fotos propias si está logueado
if ($usuario_id) {
    $excluir .= " AND usuario_id != ?";
    $params[] = $usuario_id;
}

// Consulta final
$query = "
    SELECT id, titulo FROM fotografias
    WHERE estado = 'admitida'
    $excluir
    ORDER BY RAND()
    LIMIT 2
";

file_put_contents(__DIR__ . '/duelo_debug.log', "QUERY: $query\nPARAMS: " . var_export($params, true) . "\n", FILE_APPEND);

$stmt = $pdo->prepare($query);
$stmt->execute($params);

if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error en la consulta SQL']);
    exit;
}

$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents(__DIR__ . '/duelo_debug.log', "RESULT: " . var_export($fotos, true) . "\n", FILE_APPEND);

// Siempre devolver JSON, aunque esté vacío
if (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');
echo json_encode($fotos);
