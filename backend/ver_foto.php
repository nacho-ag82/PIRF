<?php
require_once "conexion.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    http_response_code(404);
    exit;
}
$stmt = $pdo->prepare("SELECT archivo FROM fotografias WHERE id = ?");
$stmt->execute([$id]);
$img = $stmt->fetchColumn();
if (!$img) {
    http_response_code(404);
    exit;
}
// Opcional: podrías guardar el tipo MIME en la base de datos, aquí asumimos JPEG/PNG
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_buffer($finfo, $img);
finfo_close($finfo);
header("Content-Type: $mime");
echo $img;
