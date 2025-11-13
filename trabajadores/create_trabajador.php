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

$result = [];

try {
    $sql = 'INSERT INTO prueba.trabajador (tra_cod, tra_nom, tra_pat, tra_mat, est_ado)
            VALUES (:cod, :nom, :pat, :mat, 1)
            RETURNING tra_ide, tra_cod, tra_nom, tra_pat, tra_mat, est_ado';

    $stmt = $pdo->prepare($sql);

    foreach ($data as $row) {
        $stmt->execute([
            ':cod' => $row['tra_cod'] ?? 0,
            ':nom' => $row['tra_nom'] ?? '',
            ':pat' => $row['tra_pat'] ?? '',
            ':mat' => $row['tra_mat'] ?? ''
        ]);
        $result[] = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al crear trabajador: ' . $e->getMessage()
    ]);
}
