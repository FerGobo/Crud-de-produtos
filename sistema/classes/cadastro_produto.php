<?php
// Back/pages/cadastro_produto.php
// Endpoint AJAX para cadastrar novo produto

session_start();
require_once '../classes/Produto.php';
require_once '../classes/Fornecedor.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

// GET → retorna lista de fornecedores para popular o select
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $fornecedor = new Fornecedor();
    echo json_encode(['ok' => true, 'fornecedores' => $fornecedor->listar()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
    exit;
}

$nome         = trim($_POST['nome']         ?? '');
$descricao    = trim($_POST['descricao']    ?? '');
$preco        = (float)($_POST['preco']     ?? 0);
$estoque      = (int)($_POST['estoque']     ?? 0);
$fornecedor_id = (int)($_POST['fornecedor_id'] ?? 0);

if (empty($nome)) {
    echo json_encode(['ok' => false, 'msg' => 'Nome é obrigatório.']);
    exit;
}
if ($preco <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Preço inválido.']);
    exit;
}
if (!$fornecedor_id) {
    echo json_encode(['ok' => false, 'msg' => 'Selecione um fornecedor.']);
    exit;
}

$produto = new Produto();
$ok      = $produto->cadastrar($nome, $descricao, $preco, $estoque, $fornecedor_id);

echo json_encode([
    'ok'  => $ok,
    'msg' => $ok ? 'Produto cadastrado com sucesso!' : 'Erro ao cadastrar produto.'
]);
