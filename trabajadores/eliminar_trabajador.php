<?php
header('Content-Type: application/json');
include '../db.php';


$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

$data = is_assoc($input) ? [$input] : $input;

function is_assoc(array $arr): bool
{
    return array_keys($arr) !== range(0, count($arr) - 1);
}

try {
    $sql = 'UPDATE prueba.trabajador
            SET est_ado = 0
            WHERE tra_ide = :ide';

    $stmt = $pdo->prepare($sql);

    foreach ($data as $row) {
        if (!isset($row['tra_ide'])) continue;
        $stmt->execute([':ide' => $row['tra_ide']]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Trabajador eliminado (soft delete)'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al eliminar trabajador: ' . $e->getMessage()
    ]);
}
