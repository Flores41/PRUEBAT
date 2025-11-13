<?php
header('Content-Type: application/json');
include '../db.php';


try {
    $sql = 'SELECT ven_ide, ven_ser, ven_num, ven_cli, ven_mon, est_ado
            FROM prueba.venta
            WHERE est_ado = 1
            ORDER BY ven_ide DESC';

    $stmt = $pdo->query($sql);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $ventas
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error al obtener ventas: ' . $e->getMessage()
    ]);
}
