<?php
session_start();
require_once '../classes/Usuario.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
    exit;
}

$nome  = trim($_POST['nome']  ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($nome)) {
    echo json_encode(['ok' => false, 'msg' => 'Nome obrigatório.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}
if (strlen($senha) < 6) {
    echo json_encode(['ok' => false, 'msg' => 'Senha deve ter mínimo 6 caracteres.']);
    exit;
}

$usuario = new Usuario();

if ($usuario->emailExiste($email)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail já cadastrado.']);
    exit;
}

if ($usuario->cadastrar($nome, $email, $senha)) {
    echo json_encode(['ok' => true, 'msg' => 'Cadastro realizado!']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao cadastrar. Tente novamente.']);
}
exit;