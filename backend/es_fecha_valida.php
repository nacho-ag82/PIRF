<?php
require_once "conexion.php";

function estaPermitido() {
    global $pdo;
    // Selecciona el campo correspondiente de la única fila de configuración
    $stmt = $pdo->prepare("SELECT lim_subida FROM configuracion LIMIT 1");
    $stmt->execute();
    $fechaLimite = $stmt->fetchColumn();
    if (!$fechaLimite) return true; // Si no hay límite, permitir
    $ahora = date("Y-m-d H:i:s");
    return ($ahora <= $fechaLimite);
}
?>
