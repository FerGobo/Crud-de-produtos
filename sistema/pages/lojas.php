<?php
require_once '../includes/header.php';
require_once '../classes/Produto.php';
require_once '../classes/Cesta.php';

$produto = new Produto();
$cesta   = new Cesta();
$msg     = '';

// Itens já na cesta do usuário (para pré-marcar checkboxes)
$itens_cesta = $cesta->listarItens($_SESSION['usuario_id']);
$ids_na_cesta = array_column($itens_cesta, 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selecionados = $_POST['produtos'] ?? [];

    if (empty($selecionados)) {
        $msg = '<div class="alert error">Selecione ao menos um produto!</div>';
    } else {
        $cesta->adicionarProdutos($_SESSION['usuario_id'], $selecionados);
        $msg = '<div class="alert success">Cesta atualizada! <a href="cesta.php">Ver cesta →</a></div>';
        $ids_na_cesta = $selecionados;
    }
}

$lista = $produto->listar();
?>
<div class="container">
  <h2>🛍️ Loja — Monte sua Cesta</h2>
  <?= $msg ?>

  <form method="POST" id="form-loja">
    <div class="produtos-grid">
      <?php foreach ($lista as $p): ?>
        <div class="produto-card <?= in_array($p['id'], $ids_na_cesta) ? 'selecionado' : '' ?>">
          <label>
            <input type="checkbox" name="produtos[]" value="<?= $p['id'] ?>"
              <?= in_array($p['id'], $ids_na_cesta) ? 'checked' : '' ?>>
            <strong><?= htmlspecialchars($p['nome']) ?></strong>
            <span class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
            <small><?= htmlspecialchars($p['descricao']) ?></small>
            <?php if ($p['fornecedor_nome']): ?>
              <small>Fornecedor: <?= htmlspecialchars($p['fornecedor_nome']) ?></small>
            <?php endif; ?>
          </label>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="acoes-loja">
      <span id="contador">0 produto(s) selecionado(s)</span>
      <button type="submit" id="btn-adicionar" disabled>Adicionar à Cesta 🛒</button>
    </div>
  </form>
</div>
<?php require_once '../includes/footer.php'; ?>