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
    $sql = 'SELECT v_d_ide, ven_ide, v_d_pro, v_d_uni, v_d_can, v_d_tot, est_ado
            FROM prueba.venta_detalle
            WHERE ven_ide = :ven_ide AND est_ado = 1
            ORDER BY v_d_ide';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ven_ide' => $ven_ide]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $detalles
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al obtener detalle: ' . $e->getMessage()
    ]);
}
