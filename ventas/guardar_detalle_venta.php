<?php
header('Content-Type: application/json');
include '../db.php';


$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

$ven_ide = $data['ven_ide'] ?? 0;
$v_d_pro = $data['v_d_pro'] ?? '';
$v_d_uni = $data['v_d_uni'] ?? 0;
$v_d_can = $data['v_d_can'] ?? 0;

if ($ven_ide == 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Falta ven_ide']);
    exit;
}

try {
    $sql = 'INSERT INTO prueba.venta_detalle (ven_ide, v_d_pro, v_d_uni, v_d_can, est_ado)
            VALUES (?, ?, ?, ?, 1)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ven_ide, $v_d_pro, $v_d_uni, $v_d_can]);

    $sqlTotal = 'UPDATE prueba.venta
                 SET ven_mon = (
                     SELECT COALESCE(SUM(v_d_tot), 0)
                     FROM prueba.venta_detalle
                     WHERE ven_ide = ?
                 )
                 WHERE ven_ide = ?';
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute([$ven_ide, $ven_ide]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al guardar detalle: ' . $e->getMessage()
    ]);
}
