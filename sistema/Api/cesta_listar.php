<?php
session_start();
require_once '../classes/Cesta.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

$cesta = new Cesta();
$itens = $cesta->listar((int)$_SESSION['usuario_id']);
$total = array_sum(array_column($itens, 'preco'));

echo json_encode([
    'ok'    => true,
    'itens' => $itens,
    'total' => number_format($total, 2, ',', '.'),
    'qtd'   => count($itens)
]);