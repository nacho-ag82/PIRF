<?php
$file = __DIR__ . '/../documentos/bases_pirf.pdf';

if (file_exists($file)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="bases_pirf.pdf"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    http_response_code(404);
   'Archivo no encontrado.';
}
echo '<script>window.location.href = "/index.php";</script>';
exit;

?>