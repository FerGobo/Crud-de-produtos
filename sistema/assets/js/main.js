document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('input[name="produtos[]"]');
    const contador   = document.getElementById('contador');
    const btnAdicionar = document.getElementById('btn-adicionar');

    function atualizarContador() {
        const marcados = document.querySelectorAll('input[name="produtos[]"]:checked').length;
        if (contador)    contador.textContent = marcados + ' produto(s) selecionado(s)';
        if (btnAdicionar) btnAdicionar.disabled = marcados === 0;

        // Destaca card selecionado
        checkboxes.forEach(cb => {
            cb.closest('.produto-card')?.classList.toggle('selecionado', cb.checked);
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', atualizarContador));
    atualizarContador(); // estado inicial
});

function salvarFornecedor(id) {
    const row = document.getElementById('forn-' + id);
    const campos = row.querySelectorAll('.edit');
    const data = new FormData();
    data.append('acao', 'atualizar');
    data.append('id',   id);
    campos.forEach(td => data.append(td.dataset.campo, td.innerText.trim()));

    fetch('../ajax/ajax_fornecedores.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => alert(res.msg));
}

function deletarFornecedor(id) {
    if (!confirm('Excluir fornecedor?')) return;
    const data = new FormData();
    data.append('acao', 'deletar');
    data.append('id',   id);

    fetch('../ajax/ajax_fornecedores.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.ok) document.getElementById('forn-' + id).remove();
            else alert(res.msg);
        });
}

function salvarProduto(id) {
    const row = document.getElementById('prod-' + id);
    const campos = row.querySelectorAll('.edit');
    const data = new FormData();
    data.append('acao', 'atualizar');
    data.append('id',   id);
    campos.forEach(td => data.append(td.dataset.campo, td.innerText.trim()));

    fetch('../ajax/ajax_produtos.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => alert(res.msg));
}

function deletarProduto(id) {
    if (!confirm('Excluir produto?')) return;
    const data = new FormData();
    data.append('acao', 'deletar');
    data.append('id',   id);

    fetch('../ajax/ajax_produtos.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.ok) document.getElementById('prod-' + id).remove();
            else alert(res.msg);
        });
}