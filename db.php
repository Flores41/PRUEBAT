<?php


$host = 'localhost';
$port = "5432";
$db = 'pruebac';
$user = 'postgres';
$pass = '1245';


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