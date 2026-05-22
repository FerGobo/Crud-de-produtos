<?php
require_once __DIR__ . '/Database.php';

class Fornecedor {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function cadastrar(string $nome): bool {
    $stmt = $this->db->prepare(
        "INSERT INTO fornecedores (nome) VALUES (:nome)"
    );
    return $stmt->execute([':nome' => $nome]);
}

    public function listar(): array {
        return $this->db->query("SELECT * FROM fornecedores ORDER BY nome")->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM fornecedores WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function atualizar(int $id, string $nome): bool {
    $stmt = $this->db->prepare(
        "UPDATE fornecedores SET nome=:nome WHERE id=:id"
    );
    return $stmt->execute([':id' => $id, ':nome' => $nome]);
}

    public function deletar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM fornecedores WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}