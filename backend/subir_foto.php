<?php
session_start();
require_once "conexion.php";
require_once "es_fecha_valida.php";

// Manejo global de errores para devolver siempre JSON
set_exception_handler(function($e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Error interno.", "error" => $e->getMessage()]);
    exit;
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Error interno.", "error" => "$errstr in $errfile:$errline"]);
    exit;
});

// Corregir la comprobación de sesión y usuario
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'participante') {
    echo json_encode(["success" => false, "message" => "No autenticado o acceso denegado."]);
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// Cambia 'fin_subida' por 'lim_subida' para el nuevo modelo
if (!estaPermitido('lim_subida')) {
    echo json_encode(["success" => false, "message" => "El plazo de subida ha terminado."]);
    exit;
}

// Comprobar si $_FILES está vacío por límite de tamaño PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES)) {
    echo json_encode([
        "success" => false,
        "message" => "No se recibió archivo. Puede que el archivo supere el tamaño máximo permitido por el servidor."
    ]);
    exit;
}

$titulo = trim($_POST['titulo'] ?? '');
// Aceptar tanto 'foto' como 'archivo'
$archivo = $_FILES['foto'] ?? $_FILES['archivo'] ?? null;

// DEBUG: Mostrar información de $_FILES si no se recibe archivo
if (!$archivo) {
    echo json_encode([
        "success" => false,
        "message" => "No se recibió archivo.",
        "debug_files" => $_FILES
    ]);
    exit;
}

if (!$titulo || strlen($titulo) > 100) {
    echo json_encode(["success" => false, "message" => "Título no válido."]);
    exit;
}

if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "Error al subir archivo."]);
    exit;
}

// Verificar extensión y tamaño
$permitidos = ['image/jpeg', 'image/png'];
if (!in_array($archivo['type'], $permitidos)) {
    echo json_encode(["success" => false, "message" => "Formato no permitido."]);
    exit;
}

if ($archivo['size'] > 5 * 1024 * 1024) {
    echo json_encode(["success" => false, "message" => "La imagen supera el límite de 5MB."]);
    exit;
}

// Leer el contenido binario del archivo
$contenidoImagen = file_get_contents($archivo['tmp_name']);
if ($contenidoImagen === false) {
    echo json_encode(["success" => false, "message" => "No se pudo leer el archivo."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO fotografias (usuario_id, titulo, archivo, estado, fecha_subida) VALUES (?, ?, ?, 'pendiente', NOW())");
    $stmt->bindParam(1, $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $titulo, PDO::PARAM_STR);
    $stmt->bindParam(3, $contenidoImagen, PDO::PARAM_LOB);
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Foto subida correctamente. Esperando validación."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error al guardar en la base de datos.", "error" => $e->getMessage()]);
}
?>
