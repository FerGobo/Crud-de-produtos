<?php
session_start();
require_once '../classes/Produto.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

$produto = new Produto();
echo json_encode([
    'ok'      => true,
    'produtos' => $produto->listar()
]);