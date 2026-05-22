<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario_id'])) { echo json_encode(['ok'=>false,'msg'=>'Não autenticado']); exit; }

require_once '../classes/Cesta.php';
$c = new Cesta();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'adicionar':
        $ids = $_POST['produtos'] ?? [];
        $ok = $c->adicionar($_SESSION['usuario_id'], $ids);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Cesta atualizada!' : 'Erro']);
        break;
    case 'listar':
        $produtos = $c->listar($_SESSION['usuario_id']);
        echo json_encode(['ok' => true, 'produtos' => $produtos]);
        break;
    default:
        echo json_encode(['ok' => false, 'msg' => 'Ação inválida']);
}
