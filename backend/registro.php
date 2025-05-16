<?php
require_once "conexion.php";

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';

if (!$nombre || !$email || !$pass) {
    echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(["success" => false, "message" => "Email ya registrado."]);
    exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña_hash) VALUES (?, ?, ?)");
$stmt->execute([$nombre, $email, $hash]);

echo json_encode(["success" => true, "message" => "Usuario registrado correctamente."]);
?>
