<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "conexion.php";

header('Content-Type: application/json');

try {
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'];

    // Obtener pares de fotos ya votados hoy por la IP
    $votosStmt = $pdo->prepare("
        SELECT foto_ganadora_id, foto_perdedora_id
        FROM votos
        WHERE ip = ? AND DATE(fecha) = CURDATE()
    ");
    $votosStmt->execute([$ip]);
    $votos = $votosStmt->fetchAll(PDO::FETCH_ASSOC);

    // Construir un set de pares ya usados (sin importar el orden)
    $duelos_votados = [];
    foreach ($votos as $v) {
        $a = (int)$v['foto_ganadora_id'];
        $b = (int)$v['foto_perdedora_id'];
        $dueloKey = $a < $b ? "$a-$b" : "$b-$a";
        $duelos_votados[$dueloKey] = true;
    }

    // Inicializar $excluir como cadena vacía
    $excluir = "";

    // Excluir fotos propias si está logueado
    $params = [];
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

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $fotos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay suficientes fotos disponibles
    if (count($fotos_disponibles) < 2) {
        echo json_encode(['error' => 'No hay más duelos disponibles para hoy.']);
        exit;
    }

    // Seleccionar aleatoriamente dos fotos distintas que no formen un duelo ya mostrado
    $duelo = null;
    $maxIntentos = 20;
    for ($i = 0; $i < $maxIntentos; $i++) {
        shuffle($fotos_disponibles);
        $id1 = $fotos_disponibles[0]['id'];
        $id2 = $fotos_disponibles[1]['id'];
        if ($id1 == $id2) {
            continue; // Asegura que los IDs sean diferentes
        }
        $key = $id1 < $id2 ? "$id1-$id2" : "$id2-$id1";
        if (!isset($duelos_votados[$key])) {
            $duelo = [$id1, $id2];
            break;
        }
    }

        // Devuelve los datos de las dos fotos seleccionadas
        $stmt = $pdo->prepare("SELECT id FROM fotografias WHERE id IN (?, ?)");
        $stmt->execute($duelo);
        $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['duelo' => $fotos]);
    
} catch (Throwable $e) {
    echo json_encode(['error' => 'Error interno del servidor.', 'details' => $e->getMessage()]);
}
exit;
