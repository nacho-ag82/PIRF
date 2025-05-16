<?php
session_start();
require_once "conexion.php";

header('Content-Type: application/json');

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

// Verifica que la conexión PDO exista
if (!isset($pdo)) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]);
    exit;
}

// GET: devolver config (acceso público)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT lim_subida, lim_fvoto, lim_votos FROM configuracion LIMIT 1");
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($config ? $config : new stdClass());
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al obtener configuración.", "error" => $e->getMessage()]);
    }
    exit;
}

// Solo admin puede modificar configuración
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "No autorizado."]);
    exit;
}

// POST: actualizar toda la configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lim_subida = $_POST['lim_subida'] ?? null;
    $lim_fvoto = $_POST['lim_fvoto'] ?? null;
    $lim_votos = isset($_POST['lim_votos']) ? $_POST['lim_votos'] : null;

    $lim_subida = ($lim_subida === '') ? null : $lim_subida;
    $lim_fvoto = ($lim_fvoto === '') ? null : $lim_fvoto;
    $lim_votos = ($lim_votos === '' || $lim_votos === null) ? null : (int)$lim_votos;

    try {
        $stmt = $pdo->prepare("UPDATE configuracion SET lim_subida = :lim_subida, lim_fvoto = :lim_fvoto, lim_votos = :lim_votos");
        $stmt->bindValue(':lim_subida', $lim_subida ?: null, PDO::PARAM_STR);
        $stmt->bindValue(':lim_fvoto', $lim_fvoto ?: null, PDO::PARAM_STR);
        if ($lim_votos === null) {
            $stmt->bindValue(':lim_votos', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':lim_votos', $lim_votos, PDO::PARAM_INT);
        }
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Configuración actualizada."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al actualizar configuración.", "error" => $e->getMessage()]);
    }
    exit;
}
?>