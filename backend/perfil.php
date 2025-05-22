<?php
session_start();
require_once "conexion.php";
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}
$id = $_SESSION['usuario_id'];

// Endpoint de autocompletado
if (isset($_GET['autocomplete']) && in_array($_GET['autocomplete'], ['direccion', 'telefono'])) {
    $campo = $_GET['autocomplete'] === 'telefono' ? 'numero_telefono' : 'direccion';
    $term = trim($_GET['term'] ?? '');
    if ($term === '') {
        echo json_encode([]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT DISTINCT $campo FROM usuarios WHERE $campo LIKE ? AND $campo <> '' LIMIT 10");
    $stmt->execute([$term . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT nombre, email, direccion, numero_telefono, fecha_nacimiento FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user ?: []);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $nombre = trim($data['nombre'] ?? "");
    $email = trim($data['email'] ?? "");
    $direccion = trim($data['direccion'] ?? "");
    $telefono = trim($data['telefono'] ?? "");
    $fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
    $password = $data['password'] ?? "";

    if (!$nombre || !$email) {
        echo json_encode(['success' => false, 'error' => 'Nombre y email requeridos']);
        exit;
    }

    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, direccion=?, numero_telefono=?, fecha_nacimiento=?, contraseña_hash=? WHERE id=?");
        $ok = $stmt->execute([$nombre, $email, $direccion, $telefono, $fecha_nacimiento, $hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, direccion=?, numero_telefono=?, fecha_nacimiento=? WHERE id=?");
        $ok = $stmt->execute([$nombre, $email, $direccion, $telefono, $fecha_nacimiento, $id]);
    }
    echo json_encode(['success' => $ok]);
    exit;
}
echo json_encode(['error' => 'Método no permitido']);
