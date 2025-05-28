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

    // Consulta para saber cuántos votos ha hecho esta IP hoy
    $votosHoyStmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM votos
        WHERE ip = ? AND DATE(fecha) = CURDATE()
    ");
    $votosHoyStmt->execute([$ip]);
    $votosHoy = $votosHoyStmt->fetch(PDO::FETCH_ASSOC);
    $totalVotosHoy = (int)($votosHoy['total'] ?? 0);


    // Obtener pares de fotos ya votados hoy por la IP
    $votosStmt = $pdo->prepare("
        SELECT foto_ganadora_id, foto_perdedora_id
        FROM votos
        WHERE ip = ? AND DATE(fecha) = CURDATE()
    ");
    $votosStmt->execute([$ip]);
    $votos = $votosStmt->fetchAll(PDO::FETCH_ASSOC);

    // Construir un set de pares ya usados (sin importar el orden) SOLO para la IP de la petición
    $duelos_votados = [];
    foreach ($votos as $v) {
        $a = (int)$v['foto_ganadora_id'];
        $b = (int)$v['foto_perdedora_id'];
        // Solo marca como usados los pares para la IP actual
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
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $fotos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay suficientes fotos disponibles
    if (count($fotos_disponibles) < 2) {
        echo json_encode(['error' => 'No hay más duelos disponibles para hoy.']);
        exit;
    }

    // Generar todas las combinaciones posibles de duelos (pares únicos)
    $duelos_posibles = [];
    for ($i = 0; $i < count($fotos_disponibles); $i++) {
        for ($j = $i + 1; $j < count($fotos_disponibles); $j++) {
            $id1 = $fotos_disponibles[$i]['id'];
            $id2 = $fotos_disponibles[$j]['id'];
            // Solo permite el duelo si no ha sido mostrado en ningún orden
            $key = $id1 < $id2 ? "$id1-$id2" : "$id2-$id1";
            if (!isset($duelos_votados[$key])) {
                $duelos_posibles[] = [$id1, $id2];
            }
        }
    }

    // Si no hay duelos posibles, informar
    if (empty($duelos_posibles)) {
        echo json_encode(['error' => 'No hay más duelos disponibles para hoy.']);
        exit;
    }

    // Seleccionar un duelo aleatorio de los posibles
    $duelo = $duelos_posibles[array_rand($duelos_posibles)];

    // Devuelve los datos de las dos fotos seleccionadas
    $stmt = $pdo->prepare("SELECT id FROM fotografias WHERE id IN (?, ?)");
    $stmt->execute([$duelo[0], $duelo[1]]);
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['duelo' => $fotos]);
    
} catch (Throwable $e) {
    echo json_encode(['error' => 'Error interno del servidor.', 'details' => $e->getMessage()]);
}
exit;
