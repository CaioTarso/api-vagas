<?php

namespace App\Repositories;

use PDO;
use App\Models\Vaga;

/**
 * Gerencia o acesso aos dados das Vagas no banco de dados.
 */
class VagaRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Verifica se uma vaga com o ID fornecido já existe.
     */
    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM vagas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $vagaData = $stmt->fetch();
        return $vagaData ?: null;
    }

    /**
     * Cria uma nova vaga no banco de dados.
     * Retorna true em caso de sucesso, false caso contrário.
     */
    public function create(Vaga $vaga): bool {
        $sql = "INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel)
                VALUES (:id, :empresa, :titulo, :descricao, :localizacao, :nivel)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $vaga->id,
            ':empresa' => $vaga->empresa,
            ':titulo' => $vaga->titulo,
            ':descricao' => $vaga->descricao,
            ':localizacao' => $vaga->localizacao,
            ':nivel' => $vaga->nivel
        ]);
    }

    /**
     * Busca os dados de uma vaga (localizacao, nivel) pelo ID.
     * Usado no cálculo de ranking.
     */
    public function getVagaDetailsForRanking(string $id_vaga): ?array {
        $stmt = $this->db->prepare("SELECT localizacao, nivel FROM vagas WHERE id = :id_vaga");
        $stmt->execute([':id_vaga' => $id_vaga]);
        $vaga = $stmt->fetch();
        return $vaga ?: null;
    }

    /**
     * Busca candidatos de uma vaga para o ranking.
     * Esta função pode ser movida para CandidaturaRepository se fizer mais sentido,
     * ou pode receber os dados de pessoa de um PessoaRepository.
     * Por ora, deixo similar ao seu controller.
     */
    public function getCandidatosForRanking(string $id_vaga): array {
        $sql = "SELECT p.nome, p.profissao, p.localizacao AS candidato_localizacao, p.nivel AS candidato_nivel
                FROM candidaturas c
                JOIN pessoas p ON c.id_pessoa = p.id
                WHERE c.id_vaga = :id_vaga";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_vaga' => $id_vaga]);
        return $stmt->fetchAll();
    }
}