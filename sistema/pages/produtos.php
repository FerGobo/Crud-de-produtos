<?php
// Back/pages/produtos.php
// Endpoint AJAX para atualizar e deletar produtos

session_start();
require_once '../classes/Produto.php';

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

$acao    = $_POST['acao'] ?? '';
$produto = new Produto();

// ── Atualizar ──
if ($acao === 'atualizar') {
    $id        = (int)($_POST['id']       ?? 0);
    $nome      = trim($_POST['nome']      ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = (float)($_POST['preco']  ?? 0);
    $estoque   = (int)($_POST['estoque']  ?? 0);

    if (!$id || !$nome) {
        echo json_encode(['ok' => false, 'msg' => 'Dados inválidos.']);
        exit;
    }
    if ($preco <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Preço inválido.']);
        exit;
    }

    // Mantém o fornecedor_id atual para não perder o relacionamento
    $atual         = $produto->buscarPorId($id);
    $fornecedor_id = $atual ? (int)$atual['fornecedor_id'] : 0;

    $ok = $produto->atualizar($id, $nome, $descricao, $preco, $estoque, $fornecedor_id);
    echo json_encode([
        'ok'  => $ok,
        'msg' => $ok ? 'Produto atualizado!' : 'Erro ao atualizar.'
    ]);
    exit;
}

// ── Deletar ──
if ($acao === 'deletar') {
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit;
    }

    $ok = $produto->deletar($id);
    echo json_encode([
        'ok'  => $ok,
        'msg' => $ok ? 'Produto removido.' : 'Erro ao remover.'
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Ação não reconhecida.']);