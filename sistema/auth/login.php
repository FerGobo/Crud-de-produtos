<?php
// Back/pages/login.php
// Responde APENAS JSON — nunca gera HTML
// Chamado via AJAX pelo Front/login.html

session_start();
require_once '../classes/Usuario.php';

// Garante que qualquer erro do PHP saia como JSON também,
// evitando quebrar o parse no frontend
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro interno: ' . $e->getMessage()]);
    exit;
});

// Cabeçalho JSON obrigatório
header('Content-Type: application/json; charset=utf-8');

// Bloqueia acesso direto que não seja POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método não permitido.']);
    exit;
}

// Usuário já está logado — redireciona direto
if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'ok'       => true,
        'redirect' => '/Front/public/produtos.html'
    ]);
    exit;
}

// Lê e valida os campos
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}

// Tenta autenticar
$usuario = new Usuario();
$user    = $usuario->login($email, $senha);

if ($user) {
    // Salva na sessão
    $_SESSION['usuario_id']   = $user['id'];
    $_SESSION['usuario_nome'] = $user['nome'];

    echo json_encode([
        'ok'       => true,
        'redirect' => '/Front/public/produtos.html'
    ]);
} else {
    echo json_encode([
        'ok'  => false,
        'msg' => 'E-mail ou senha incorretos.'
    ]);
}
exit;
