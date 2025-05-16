<?php
$host = 'localhost'; // Database host
$dbname = 'RF'; // Cambia esto por el nombre real de tu base de datos
$username = 'root'; // Cambia esto por tu usuario real de MySQL
$password = ''; // Cambia esto por tu contraseña real de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>