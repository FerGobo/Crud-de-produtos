<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado']);
    exit;
}

require_once '../classes/Produto.php';
$p = new Produto();

// GET → listar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $produtos = $p->listar();
    echo json_encode(['ok' => true, 'produtos' => $produtos]);
    exit;
}

// POST → ações
$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'cadastrar':
        $ok = $p->cadastrar(
            trim($_POST['nome']),
            trim($_POST['descricao'] ?? ''),
            (float)$_POST['preco'],
            (int)$_POST['estoque'],
            (int)($_POST['fornecedor_id'] ?? 0)
        );
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Cadastrado!' : 'Erro']);
        break;

    case 'atualizar':
        $ok = $p->atualizar(
            (int)$_POST['id'],
            trim($_POST['nome']),
            trim($_POST['descricao'] ?? ''),
            (float)$_POST['preco'],
            (int)$_POST['estoque'],
            (int)($_POST['fornecedor_id'] ?? 0)
        );
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Atualizado!' : 'Erro']);
        break;

    case 'deletar':
        $ok = $p->deletar((int)$_POST['id']);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Excluído!' : 'Erro']);
        break;

    default:
        echo json_encode(['ok' => false, 'msg' => 'Ação inválida']);
}