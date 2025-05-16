<?php
session_start();
require_once "conexion.php";

$email = $_POST['email'];
$pass = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($pass, $user['contraseña_hash'])) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['rol'] = $user['rol'];
    echo json_encode(["success" => true, "rol" => $user['rol']]);
} else {
    echo json_encode(["success" => false]);
}
?>
