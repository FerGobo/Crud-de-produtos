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

$produto_ids = $_POST['produto_ids'] ?? [];

if (empty($produto_ids)) {
    echo json_encode(['ok' => false, 'msg' => 'Nenhum produto selecionado.']);
    exit;
}

$cesta = new Cesta();
$ok    = $cesta->adicionar((int)$_SESSION['usuario_id'], $produto_ids);

if ($ok) {
    echo json_encode(['ok' => true, 'msg' => count($produto_ids) . ' produto(s) adicionado(s) à cesta!']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao adicionar à cesta.']);
}