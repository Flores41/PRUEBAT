<?php

// Copia este archivo a db.php y configura tus credenciales

$host = 'localhost';
$port = "5432";
$db = 'tu_base_de_datos';
$user = 'tu_usuario';
$pass = 'tu_contraseña';


$cone = "pgsql:host=$host; port=$port; dbname=$db; user=$user; password=$pass";

try {

    $pdo = new PDO($cone);
} catch (PDOException $e) {


    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de conexión: ' . $e->getMessage()
    ]);
    exit;
}
