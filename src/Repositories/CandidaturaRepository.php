<?php

namespace App\Repositories;

use PDO;
use App\Models\Candidatura;

/**
 * Gerencia o acesso aos dados das Candidaturas no banco de dados.
 */
class CandidaturaRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Verifica se uma candidatura com o ID fornecido já existe.
     */
    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT id FROM candidaturas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    /**
     * Verifica se já existe uma candidatura para a mesma pessoa e vaga.
     * A regra é "NÃO DEVE ter duplicidade nas candidaturas".
     */
    public function findByVagaAndPessoa(string $id_vaga, string $id_pessoa): ?array {
        $stmt = $this->db->prepare("SELECT id FROM candidaturas WHERE id_vaga = :id_vaga AND id_pessoa = :id_pessoa");
        $stmt->execute([':id_vaga' => $id_vaga, ':id_pessoa' => $id_pessoa]);
        $data = $stmt->fetch();
        return $data ?: null;
    }

    /**
     * Cria uma nova candidatura.
     */
    public function create(Candidatura $candidatura): bool {
        $sql = "INSERT INTO candidaturas (id, id_vaga, id_pessoa) VALUES (:id, :id_vaga, :id_pessoa)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $candidatura->id,
            ':id_vaga' => $candidatura->id_vaga,
            ':id_pessoa' => $candidatura->id_pessoa
        ]);
    }
}