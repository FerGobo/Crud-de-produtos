<?php
require_once __DIR__ . '/Database.php';

class Produto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function cadastrar(string $nome, string $descricao, float $preco, int $estoque, int $fornecedor_id): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO produtos (nome, descricao, preco, estoque, fornecedor_id)
             VALUES (:nome, :descricao, :preco, :estoque, :fornecedor_id)"
        );
        return $stmt->execute([
            ':nome'          => $nome,
            ':descricao'     => $descricao,
            ':preco'         => $preco,
            ':estoque'       => $estoque,
            ':fornecedor_id' => $fornecedor_id,
        ]);
    }

    public function listar(): array {
        return $this->db->query(
            "SELECT p.*, f.nome AS fornecedor_nome
             FROM produtos p
             LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
             ORDER BY p.nome"
        )->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, f.nome AS fornecedor_nome
             FROM produtos p
             LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
             WHERE p.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function atualizar(int $id, string $nome, string $descricao, float $preco, int $estoque, int $fornecedor_id): bool {
        $stmt = $this->db->prepare(
            "UPDATE produtos SET nome=:nome, descricao=:descricao, preco=:preco,
             estoque=:estoque, fornecedor_id=:fornecedor_id WHERE id=:id"
        );
        return $stmt->execute([
            ':id'            => $id,
            ':nome'          => $nome,
            ':descricao'     => $descricao,
            ':preco'         => $preco,
            ':estoque'       => $estoque,
            ':fornecedor_id' => $fornecedor_id,
        ]);
    }

    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM produtos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}