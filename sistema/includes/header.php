<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Produtos</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav>
  <span>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
  <a href="dashboard.php">Dashboard</a>
  <a href="produtos.php">Produtos</a>
  <a href="fornecedores.php">Fornecedores</a>
  <a href="loja.php">Loja</a>
  <a href="cesta.php">🛒 Cesta</a>
  <a href="../auth/logout.php">Sair</a>
</nav>