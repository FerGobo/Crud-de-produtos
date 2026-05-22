<?php
require_once '../includes/header.php';
require_once '../classes/Cesta.php';

$cesta = new Cesta();

// Remover item individual
if (isset($_GET['remover'])) {
    $cesta->removerItem($_SESSION['usuario_id'], (int)$_GET['remover']);
    header('Location: cesta.php'); exit;
}

// Limpar cesta
if (isset($_GET['limpar'])) {
    $cesta->limpar($_SESSION['usuario_id']);
    header('Location: cesta.php'); exit;
}

$itens = $cesta->listarItens($_SESSION['usuario_id']);
$total = array_sum(array_column($itens, 'preco'));
$qtd   = count($itens);
?>
<div class="container">
  <h2>🛒 Minha Cesta</h2>

  <?php if (empty($itens)): ?>
    <div class="alert">Sua cesta está vazia. <a href="loja.php">Ir para a loja</a></div>
  <?php else: ?>
    <table>
      <thead><tr><th>Produto</th><th>Descrição</th><th>Preço</th><th>Remover</th></tr></thead>
      <tbody>
      <?php foreach ($itens as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['nome']) ?></td>
          <td><?= htmlspecialchars($item['descricao']) ?></td>
          <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
          <td><a href="?remover=<?= $item['id'] ?>" onclick="return confirm('Remover?')">🗑️</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <div class="resumo-cesta">
      <p>📦 Total de produtos: <strong><?= $qtd ?></strong></p>
      <p>💰 Valor total: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></p>
      <a href="?limpar=1" class="btn-danger" onclick="return confirm('Limpar toda a cesta?')">
        🗑️ Limpar Cesta
      </a>
      <a href="loja.php" class="btn">← Continuar comprando</a>
    </div>
  <?php endif; ?>
</div>
<?php require_once '../includes/footer.php'; ?>