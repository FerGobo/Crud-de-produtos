<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario_id'])) { echo json_encode(['ok'=>false,'msg'=>'Não autenticado']); exit; }

require_once '../classes/Fornecedor.php';
$f   = new Fornecedor();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($acao)) {
    // Listar fornecedores (apenas com nome preenchido)
    $fornecedores = array_filter($f->listar(), function($forn) {
        return !empty($forn['nome']);
    });
    echo json_encode(['ok' => true, 'fornecedores' => array_values($fornecedores)]);
    exit;
}

switch ($acao) {
    case 'cadastrar':
        $ok = $f->cadastrar(trim($_POST['nome']));
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Cadastrado!' : 'Erro']);
        break;
    case 'atualizar':
        $ok = $f->atualizar((int)$_POST['id'], trim($_POST['nome']));
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Atualizado!' : 'Erro']);
        break;
    case 'deletar':
        $ok = $f->deletar((int)$_POST['id']);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Excluído!' : 'Erro']);
        break;
    default:
        echo json_encode(['ok' => false, 'msg' => 'Ação inválida']);
}