<?php
// Back/Api/fornecedores.php
// Endpoint único que trata todas as operações via AJAX
// GET           → lista todos
// POST cadastrar → cadastra novo
// POST atualizar → edita existente
// POST deletar   → remove

session_start();
require_once '../classes/Fornecedor.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado.']);
    exit;
}

$fornecedor = new Fornecedor();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

// ── GET: listar ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['ok' => true, 'fornecedores' => $fornecedor->listar()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
    exit;
}

// ── POST: cadastrar ──
if ($acao === 'cadastrar') {
    $nome     = trim($_POST['nome']     ?? '');
    $cnpj     = trim($_POST['cnpj']     ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email']    ?? '');

    if (empty($nome)) {
        echo json_encode(['ok' => false, 'msg' => 'Nome obrigatório.']);
        exit;
    }

    $ok = $fornecedor->cadastrar($nome, $cnpj, $telefone, $email);
    echo json_encode([
        'ok'  => $ok,
        'msg' => $ok ? 'Fornecedor cadastrado!' : 'Erro ao cadastrar.',
        'fornecedores' => $fornecedor->listar()
    ]);
    exit;
}

// ── POST: atualizar ──
if ($acao === 'atualizar') {
    $id       = (int)($_POST['id']       ?? 0);
    $nome     = trim($_POST['nome']     ?? '');
    $cnpj     = trim($_POST['cnpj']     ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email']    ?? '');

    if (!$id || empty($nome)) {
        echo json_encode(['ok' => false, 'msg' => 'Dados inválidos.']);
        exit;
    }

    $ok = $fornecedor->atualizar($id, $nome, $cnpj, $telefone, $email);
    echo json_encode([
        'ok'  => $ok,
        'msg' => $ok ? 'Fornecedor atualizado!' : 'Erro ao atualizar.'
    ]);
    exit;
}

// ── POST: deletar ──
if ($acao === 'deletar') {
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
        exit;
    }

    $ok = $fornecedor->deletar($id);
    echo json_encode([
        'ok'  => $ok,
        'msg' => $ok ? 'Fornecedor removido.' : 'Erro ao remover.'
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Ação não reconhecida.']);
