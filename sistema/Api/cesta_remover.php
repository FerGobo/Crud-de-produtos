<?php
session_start();
require_once '../classes/Cesta.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
    exit;
}

$produto_id = (int)($_POST['produto_id'] ?? 0);

if (!$produto_id) {
    echo json_encode(['ok' => false, 'msg' => 'Produto inválido.']);
    exit;
}

$cesta = new Cesta();
$ok    = $cesta->removerItem((int)$_SESSION['usuario_id'], $produto_id);

echo json_encode([
    'ok'  => $ok,
    'msg' => $ok ? 'Produto removido.' : 'Erro ao remover.'
]);