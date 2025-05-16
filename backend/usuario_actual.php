<?php
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['usuario_id'])) {
    require_once "conexion.php";
    $stmt = $pdo->prepare("SELECT nombre, rol FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(["success" => true, "nombre" => $user['nombre'], "rol" => $user['rol']]);
    } else {
        echo json_encode(["success" => false]);
    }
} else {
    echo json_encode(["success" => false]);
}
?>
