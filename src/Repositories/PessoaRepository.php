<?php

namespace App\Repositories;

use PDO;
use App\Models\Pessoa;

/**
 * Gerencia o acesso aos dados das Pessoas no banco de dados.
 */
class PessoaRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Verifica se uma pessoa com o ID fornecido já existe.
     */
    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT id FROM pessoas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    /**
     * Cria uma nova pessoa no banco de dados.
     */
    public function create(Pessoa $pessoa): bool {
        $sql = "INSERT INTO pessoas (id, nome, profissao, localizacao, nivel)
                VALUES (:id, :nome, :profissao, :localizacao, :nivel)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $pessoa->id,
            ':nome' => $pessoa->nome,
            ':profissao' => $pessoa->profissao,
            ':localizacao' => $pessoa->localizacao,
            ':nivel' => $pessoa->nivel
        ]);
    }
}