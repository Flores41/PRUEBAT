<?php
header('Content-Type: application/json');
include '../db.php';

try {
    $sql = 'SELECT tra_ide, tra_cod, tra_nom, tra_pat, tra_mat, est_ado 
            FROM prueba.trabajador
            WHERE est_ado = 1
            ORDER BY tra_ide';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $rows
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener trabajadores: ' . $e->getMessage()
    ]);
}
