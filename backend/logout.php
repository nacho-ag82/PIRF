<?php
session_start();
session_unset();
session_destroy();
echo json_encode(["success" => true, "message" => "Sesión cerrada."]);
header("Location: login.php");
exit();