<?php
require_once __DIR__ . '/Database.php';

class Cesta {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Busca ou cria a cesta ativa do usuário
    public function obterOuCriarCesta(int $usuario_id): int {
        $stmt = $this->db->prepare(
            "SELECT id FROM cestas WHERE usuario_id = :uid ORDER BY criado_em DESC LIMIT 1"
        );
        $stmt->execute([':uid' => $usuario_id]);
        $cesta = $stmt->fetch();

        if ($cesta) return (int) $cesta['id'];

        $stmt = $this->db->prepare("INSERT INTO cestas (usuario_id) VALUES (:uid)");
        $stmt->execute([':uid' => $usuario_id]);
        return (int) $this->db->lastInsertId();
    }

    public function adicionar(int $usuario_id, array $produto_ids): bool {
        $cesta_id = $this->obterOuCriarCesta($usuario_id);

        // Remove itens anteriores e reinsere (cesta = seleção atual)
        $stmt = $this->db->prepare("DELETE FROM cesta_produtos WHERE cesta_id = :cid");
        $stmt->execute([':cid' => $cesta_id]);

        $stmt = $this->db->prepare(
            "INSERT INTO cesta_produtos (cesta_id, produto_id) VALUES (:cid, :pid)"
        );
        foreach ($produto_ids as $pid) {
            $stmt->execute([':cid' => $cesta_id, ':pid' => (int)$pid]);
        }
        return true;
    }

    public function listar(int $usuario_id): array {
    $stmt = $this->db->prepare(
        "SELECT p.id, p.nome, p.descricao, p.preco, f.nome AS fornecedor_nome
         FROM cesta_produtos ci
         JOIN cestas c      ON ci.cesta_id   = c.id
         JOIN produtos p    ON ci.produto_id = p.id
         LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
         WHERE c.usuario_id = :uid
         ORDER BY p.nome"
    );
    $stmt->execute([':uid' => $usuario_id]);
    return $stmt->fetchAll();
    }

    public function removerItem(int $usuario_id, int $produto_id): bool {
        $stmt = $this->db->prepare(
            "DELETE ci FROM cesta_produtos ci
             JOIN cestas c ON ci.cesta_id = c.id
             WHERE c.usuario_id = :uid AND ci.produto_id = :pid"
        );
        return $stmt->execute([':uid' => $usuario_id, ':pid' => $produto_id]);
    }

    public function limpar(int $usuario_id): bool {
        $stmt = $this->db->prepare(
            "DELETE ci FROM cesta_produtos ci
             JOIN cestas c ON ci.cesta_id = c.id
             WHERE c.usuario_id = :uid"
        );
        return $stmt->execute([':uid' => $usuario_id]);
    }
}