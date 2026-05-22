// App/public/js/app.js
// ─────────────────────────────────────────────────────────────
//  Cada bloco só roda se o elemento-chave da página existir,
//  então este arquivo único funciona em todas as páginas.
// ─────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

  // ══════════════════════════════════════════
  //  LOGIN  (login.html)
  // ══════════════════════════════════════════
  const loginForm = document.getElementById('loginForm');

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const email = document.getElementById('email').value.trim();
      const senha = document.getElementById('senha').value;
      const msgEl = document.getElementById('loginMsg');
      const btnEl = loginForm.querySelector('button[type="submit"]');

      if (!email || !senha) {
        mostrarMensagem(msgEl, 'Preencha todos os campos.', 'danger');
        return;
      }

      btnEl.disabled    = true;
      btnEl.textContent = 'Aguarde...';
      mostrarMensagem(msgEl, '', '');

      fetch('/Back/auth/login.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(email) +
              '&senha=' + encodeURIComponent(senha)
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.ok) {
          mostrarMensagem(msgEl, 'Login realizado! Redirecionando...', 'success');
          window.location.href = data.redirect;
        } else {
          mostrarMensagem(msgEl, data.msg || 'Erro desconhecido.', 'danger');
          btnEl.disabled    = false;
          btnEl.textContent = 'Entrar';
        }
      })
      .catch(function () {
        mostrarMensagem(msgEl, 'Não foi possível conectar ao servidor.', 'danger');
        btnEl.disabled    = false;
        btnEl.textContent = 'Entrar';
      });
    });
  }

  // ══════════════════════════════════════════
  //  CADASTRO  (cadastro.html)
  // ══════════════════════════════════════════
  const cadastroForm = document.getElementById('cadastroForm');

  if (cadastroForm) {
    cadastroForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const nome  = document.getElementById('nome').value.trim();
      const email = document.getElementById('email').value.trim();
      const senha = document.getElementById('senha').value;
      const msgEl = document.getElementById('cadastroMsg');
      const btnEl = cadastroForm.querySelector('button[type="submit"]');

      if (!nome || !email || !senha) {
        mostrarMensagem(msgEl, 'Preencha todos os campos.', 'danger');
        return;
      }

      btnEl.disabled    = true;
      btnEl.textContent = 'Aguarde...';
      mostrarMensagem(msgEl, '', '');

      fetch('/Back/auth/register.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'nome='  + encodeURIComponent(nome)  +
              '&email=' + encodeURIComponent(email) +
              '&senha=' + encodeURIComponent(senha)
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.ok) {
          mostrarMensagem(msgEl, 'Conta criada! Redirecionando...', 'success');
          setTimeout(function () {
            window.location.href = '/Front/public/login.html';
          }, 1500);
        } else {
          mostrarMensagem(msgEl, data.msg, 'danger');
          btnEl.disabled    = false;
          btnEl.textContent = 'Cadastrar';
        }
      })
      .catch(function () {
        mostrarMensagem(msgEl, 'Não foi possível conectar ao servidor.', 'danger');
        btnEl.disabled    = false;
        btnEl.textContent = 'Cadastrar';
      });
    });
  }

  // ══════════════════════════════════════════
  //  PRODUTOS  (produtos.html)
  //  Lista com checkbox + edição inline AJAX
  // ══════════════════════════════════════════
  const produtosTable = document.getElementById('produtosTable');

  if (produtosTable) {
    carregarProdutos();

    function carregarProdutos() {
      fetch('/Back/api/produtos_listar.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(function (data) {
        if (!data.ok || data.produtos.length === 0) {
          produtosTable.innerHTML = `
            <tr><td colspan="7" class="text-center text-muted">Nenhum produto cadastrado.</td></tr>`;
          return;
        }

        produtosTable.innerHTML = data.produtos.map(function (p) {
          return `
            <tr id="prod-${p.id}">
              <td><input type="checkbox" name="produto_ids[]" value="${p.id}" class="produto-check"></td>
              <td><input class="campo-editavel form-control form-control-sm"
                         data-id="${p.id}" data-campo="nome"
                         value="${p.nome ?? ''}"></td>
              <td><input class="campo-editavel form-control form-control-sm"
                         data-id="${p.id}" data-campo="descricao"
                         value="${p.descricao ?? ''}"></td>
              <td><input class="campo-editavel form-control form-control-sm" type="number" step="0.01"
                         data-id="${p.id}" data-campo="preco"
                         value="${parseFloat(p.preco).toFixed(2)}"></td>
              <td><input class="campo-editavel form-control form-control-sm" type="number"
                         data-id="${p.id}" data-campo="estoque"
                         value="${p.estoque}"></td>
              <td>${p.fornecedor_nome ?? '—'}</td>
              <td>
                <button class="btn-salvar" title="Salvar" onclick="salvarProduto(${p.id})">💾</button>
                <button class="btn-excluir" title="Excluir" onclick="deletarProduto(${p.id})">🗑️</button>
              </td>
            </tr>`;
        }).join('');

        document.getElementById('checkAll').addEventListener('change', function () {
          document.querySelectorAll('.produto-check')
            .forEach(cb => cb.checked = this.checked);
        });
      })
      .catch(function () {
        produtosTable.innerHTML = `
          <tr><td colspan="7" class="text-center text-danger">Erro ao carregar produtos.</td></tr>`;
      });
    }

    // ── Salvar edição inline ──
    window.salvarProduto = function (id) {
      const row    = document.getElementById('prod-' + id);
      const campos = row.querySelectorAll('.campo-editavel');
      const val    = {};
      campos.forEach(c => val[c.dataset.campo] = c.value.trim());

      if (!val.nome)           { alert('Nome é obrigatório.'); return; }
      if (!val.preco || parseFloat(val.preco) <= 0) { alert('Preço inválido.'); return; }

      fetch('/Back/pages/produtos.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'acao=atualizar' +
              '&id='        + id +
              '&nome='      + encodeURIComponent(val.nome) +
              '&descricao=' + encodeURIComponent(val.descricao ?? '') +
              '&preco='     + encodeURIComponent(val.preco) +
              '&estoque='   + encodeURIComponent(val.estoque ?? 0)
      })
      .then(res => res.json())
      .then(function (data) {
        row.style.transition = 'background 0.3s';
        row.style.background = data.ok ? '#d4edda' : '#f8d7da';
        setTimeout(() => row.style.background = '', 1500);
      })
      .catch(function () { alert('Erro ao salvar produto.'); });
    };

    // ── Deletar produto ──
    window.deletarProduto = function (id) {
      if (!confirm('Remover este produto?')) return;

      fetch('/Back/pages/produtos.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'acao=deletar&id=' + id
      })
      .then(res => res.json())
      .then(function (data) {
        if (data.ok) document.getElementById('prod-' + id).remove();
        else alert(data.msg);
      })
      .catch(function () { alert('Erro ao remover produto.'); });
    };

    // ── Adicionar à cesta ──
    document.getElementById('produtosForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const selecionados = [...document.querySelectorAll('.produto-check:checked')]
        .map(cb => cb.value);
      const msgEl = document.getElementById('produtosMsg');

      if (selecionados.length === 0) {
        msgEl.className   = 'mt-3 text-center text-danger';
        msgEl.textContent = 'Selecione pelo menos um produto.';
        return;
      }

      fetch('/Back/Api/cesta_adicionar.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: selecionados.map(id => 'produto_ids[]=' + id).join('&')
      })
      .then(res => res.json())
      .then(function (data) {
        msgEl.className   = 'mt-3 text-center text-' + (data.ok ? 'success' : 'danger');
        msgEl.textContent = data.msg;
      })
      .catch(function () {
        msgEl.className   = 'mt-3 text-center text-danger';
        msgEl.textContent = 'Erro ao adicionar à cesta.';
      });
    });
  }

  // ══════════════════════════════════════════
  //  CESTA  (cesta.html)
  // ══════════════════════════════════════════
  const cestaTable = document.getElementById('cestaTable');

  if (cestaTable) {
    carregarCesta();

    function carregarCesta() {
      fetch('/Back/Api/cesta_listar.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(function (data) {
        if (!data.ok || data.itens.length === 0) {
          cestaTable.innerHTML = `
            <tr><td colspan="5" class="text-center text-muted">Sua cesta está vazia.</td></tr>`;
          document.getElementById('cestaQtd').textContent   = '0';
          document.getElementById('cestaTotal').textContent = '0,00';
          return;
        }

        cestaTable.innerHTML = data.itens.map(function (item) {
          return `
            <tr>
              <td>${item.nome}</td>
              <td>${item.descricao ?? '—'}</td>
              <td>${item.fornecedor_nome ?? '—'}</td>
              <td>R$ ${parseFloat(item.preco).toFixed(2).replace('.', ',')}</td>
              <td>
                <button class="btn btn-sm btn-danger"
                  onclick="removerDaCesta(${item.id})">🗑️ Remover</button>
              </td>
            </tr>`;
        }).join('');

        document.getElementById('cestaQtd').textContent   = data.qtd;
        document.getElementById('cestaTotal').textContent = data.total;
      })
      .catch(function () {
        cestaTable.innerHTML = `
          <tr><td colspan="5" class="text-center text-danger">Erro ao carregar a cesta.</td></tr>`;
      });
    }

    window.removerDaCesta = function (produto_id) {
      if (!confirm('Remover este produto da cesta?')) return;

      fetch('/Back/Api/cesta_remover.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'produto_id=' + produto_id
      })
      .then(res => res.json())
      .then(function (data) {
        if (data.ok) carregarCesta();
      });
    };
  }

  // ══════════════════════════════════════════
  //  FORNECEDORES  (fornecedores.html)
  // ══════════════════════════════════════════
  const fornecedoresTable = document.getElementById('fornecedoresTable');

  if (fornecedoresTable) {
    carregarFornecedores();

    function carregarFornecedores() {
      fetch('/Back/pages/fornecedores.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(function (data) {
        if (!data.ok || data.fornecedores.length === 0) {
          fornecedoresTable.innerHTML = `
            <tr><td colspan="2" class="text-center text-muted">Nenhum fornecedor cadastrado.</td></tr>`;
          return;
        }
        renderFornecedores(data.fornecedores);
      })
      .catch(function () {
        fornecedoresTable.innerHTML = `
          <tr><td colspan="2" class="text-center text-danger">Erro ao carregar fornecedores.</td></tr>`;
      });
    }

    function renderFornecedores(lista) {
      fornecedoresTable.innerHTML = lista.map(function (f) {
        return `
          <tr id="forn-${f.id}">
            <td><input class="campo-editavel form-control form-control-sm"
                       data-id="${f.id}" data-campo="nome"
                       value="${f.nome ?? ''}"></td>
            <td>
              <button class="btn-salvar" title="Salvar"
                      onclick="salvarFornecedor(${f.id})">💾</button>
              <button class="btn-excluir" title="Excluir"
                      onclick="deletarFornecedor(${f.id})">🗑️</button>
            </td>
          </tr>`;
      }).join('');
    }

    document.getElementById('fornecedorForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const nome  = document.getElementById('fNome').value.trim();
      const msgEl = document.getElementById('fornecedorMsg');

      if (!nome) {
        mostrarMensagem(msgEl, 'Nome é obrigatório.', 'danger');
        return;
      }

      fetch('/Back/pages/fornecedores.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'acao=cadastrar&nome=' + encodeURIComponent(nome)
      })
      .then(res => res.json())
      .then(function (data) {
        mostrarMensagem(msgEl, data.msg, data.ok ? 'success' : 'danger');
        if (data.ok) {
          document.getElementById('fornecedorForm').reset();
          renderFornecedores(data.fornecedores);
        }
      })
      .catch(function () {
        mostrarMensagem(msgEl, 'Erro ao conectar ao servidor.', 'danger');
      });
    });

    window.salvarFornecedor = function (id) {
      const row    = document.getElementById('forn-' + id);
      const campos = row.querySelectorAll('.campo-editavel');
      const val    = {};
      campos.forEach(c => val[c.dataset.campo] = c.value.trim());

      if (!val.nome) { alert('Nome é obrigatório.'); return; }

      fetch('/Back/pages/fornecedores.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'acao=atualizar&id=' + id + '&nome=' + encodeURIComponent(val.nome)
      })
      .then(res => res.json())
      .then(function (data) {
        row.style.transition = 'background 0.3s';
        row.style.background = data.ok ? '#d4edda' : '#f8d7da';
        setTimeout(() => row.style.background = '', 1500);
      })
      .catch(function () { alert('Erro ao salvar.'); });
    };

    window.deletarFornecedor = function (id) {
      if (!confirm('Remover este fornecedor?')) return;

      fetch('/Back/pages/fornecedores.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'acao=deletar&id=' + id
      })
      .then(res => res.json())
      .then(function (data) {
        if (data.ok) document.getElementById('forn-' + id).remove();
        else alert(data.msg);
      })
      .catch(function () { alert('Erro ao remover.'); });
    };
  }

    // ══════════════════════════════════════════
  //  CADASTRO DE PRODUTO  (cadastro_produto.html)
  // ══════════════════════════════════════════
  const cadastroProdutoForm = document.getElementById('cadastroProdutoForm');

  if (cadastroProdutoForm) {

    // Carrega fornecedores no select ao abrir a página
    fetch('/Back/pages/cadastro_produto.php', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(function (data) {
      if (data.ok && data.fornecedores.length > 0) {
        const sel = document.getElementById('pFornecedor');
        data.fornecedores.forEach(function (f) {
          const opt = document.createElement('option');
          opt.value       = f.id;
          opt.textContent = f.nome;
          sel.appendChild(opt);
        });
      }
    });

    // Submit do formulário
    cadastroProdutoForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const nome          = document.getElementById('pNome').value.trim();
      const descricao     = document.getElementById('pDescricao').value.trim();
      const preco         = document.getElementById('pPreco').value;
      const estoque       = document.getElementById('pEstoque').value;
      const fornecedor_id = document.getElementById('pFornecedor').value;
      const msgEl         = document.getElementById('cadastroProdutoMsg');
      const btnEl         = cadastroProdutoForm.querySelector('button[type="submit"]');

      if (!nome) {
        mostrarMensagem(msgEl, 'Nome é obrigatório.', 'danger');
        return;
      }
      if (!preco || parseFloat(preco) <= 0) {
        mostrarMensagem(msgEl, 'Informe um preço válido.', 'danger');
        return;
      }
      if (!fornecedor_id) {
        mostrarMensagem(msgEl, 'Selecione um fornecedor.', 'danger');
        return;
      }

      btnEl.disabled    = true;
      btnEl.textContent = 'Salvando...';
      mostrarMensagem(msgEl, '', '');

      fetch('/Back/pages/cadastro_produto.php', {
        method: 'POST',
        headers: {
          'Content-Type':     'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'nome='          + encodeURIComponent(nome) +
              '&descricao='    + encodeURIComponent(descricao) +
              '&preco='        + encodeURIComponent(preco) +
              '&estoque='      + encodeURIComponent(estoque) +
              '&fornecedor_id='+ encodeURIComponent(fornecedor_id)
      })
      .then(res => res.json())
      .then(function (data) {
        mostrarMensagem(msgEl, data.msg, data.ok ? 'success' : 'danger');
        if (data.ok) cadastroProdutoForm.reset();
        btnEl.disabled    = false;
        btnEl.textContent = 'Salvar Produto';
      })
      .catch(function () {
        mostrarMensagem(msgEl, 'Erro ao conectar ao servidor.', 'danger');
        btnEl.disabled    = false;
        btnEl.textContent = 'Salvar Produto';
      });
    });
  }


  // ── Função auxiliar compartilhada ──
  function mostrarMensagem(el, texto, tipo) {
    if (!el) return;
    el.textContent = texto;
    el.className   = texto ? 'mt-3 text-center text-' + tipo : '';
  }

});