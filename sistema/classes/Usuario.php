<?php
require_once __DIR__ . '/Database.php';

class Usuario {
    private PDO $db;
    public int $id;
    public string $nome;
    public string $email;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Hash SHA256
    private function hashSenha(string $senha): string {
        return hash('sha256', $senha);
    }

    public function cadastrar(string $nome, string $email, string $senha): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)"
        );
        return $stmt->execute([
            ':nome'      => $nome,
            ':email'     => $email,
            ':senha_hash'=> $this->hashSenha($senha),
        ]);
    }

    public function login(string $email, string $senha): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = :email AND senha_hash = :senha_hash LIMIT 1"
        );
        $stmt->execute([
            ':email'     => $email,
            ':senha_hash'=> $this->hashSenha($senha),
        ]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function emailExiste(string $email): bool {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetch();
    }
}