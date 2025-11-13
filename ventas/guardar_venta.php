<?php
header('Content-Type: application/json');
include '../db.php';


$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'JSON inválido'
    ]);
    exit;
}

$ven_ide = isset($data['ven_ide']) ? (int)$data['ven_ide'] : 0;
$ven_ser = $data['ven_ser'] ?? '';
$ven_num = $data['ven_num'] ?? '';
$ven_cli = $data['ven_cli'] ?? '';


try {

    if ($ven_ide > 0) {

        $sql = 'UPDATE prueba.venta
                SET ven_ser = ?, ven_num = ?, ven_cli = ?
                WHERE ven_ide = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ven_ser, $ven_num, $ven_cli, $ven_ide]);
    } else {
        $sql = 'INSERT INTO prueba.venta (ven_ser, ven_num, ven_cli, ven_mon, est_ado)
                VALUES (?, ?, ?, 0, 1)
                RETURNING ven_ide';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ven_ser, $ven_num, $ven_cli]);
        $ven_ide = (int) $stmt->fetchColumn();
    }

    echo json_encode([
        'success' => true,
        'ven_ide' => $ven_ide
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al guardar venta: ' . $e->getMessage()
    ]);
}
