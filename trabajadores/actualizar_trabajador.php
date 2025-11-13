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
    $sql = 'UPDATE prueba.trabajador
            SET tra_cod = :cod,
                tra_nom = :nom,
                tra_pat = :pat,
                tra_mat = :mat
            WHERE tra_ide = :ide
            RETURNING tra_ide, tra_cod, tra_nom, tra_pat, tra_mat, est_ado';

    $stmt = $pdo->prepare($sql);

    foreach ($data as $row) {
        if (!isset($row['tra_ide'])) {
            continue;
        }

        $stmt->execute([
            ':cod' => $row['tra_cod'] ?? 0,
            ':nom' => $row['tra_nom'] ?? '',
            ':pat' => $row['tra_pat'] ?? '',
            ':mat' => $row['tra_mat'] ?? '',
            ':ide' => $row['tra_ide']
        ]);

        $updated = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($updated) {
            $result[] = $updated;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al actualizar trabajador: ' . $e->getMessage()
    ]);
}
