<?php
header('Content-Type: application/json');
include '../db.php';


if (!isset($_GET['ven_ide'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Falta ven_ide']);
    exit;
}

$ven_ide = (int) $_GET['ven_ide'];

try {
    $sql = 'UPDATE prueba.venta
            SET est_ado = 0
            WHERE ven_ide = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ven_ide]);

    echo json_encode([
        'success' => true,
        'message' => 'Venta eliminada'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al eliminar venta: ' . $e->getMessage()
    ]);
}
