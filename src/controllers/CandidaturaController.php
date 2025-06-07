<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\CandidaturaRepository;
use App\Repositories\VagaRepository;
use App\Repositories\PessoaRepository;
use App\Models\Candidatura;
use PDOException;

class CandidaturaController {
    private CandidaturaRepository $candidaturaRepository;
    private VagaRepository $vagaRepository;
    private PessoaRepository $pessoaRepository;

    public function __construct(
        CandidaturaRepository $candidaturaRepository,
        VagaRepository $vagaRepository,
        PessoaRepository $pessoaRepository
    ) {
        $this->candidaturaRepository = $candidaturaRepository;
        $this->vagaRepository = $vagaRepository;
        $this->pessoaRepository = $pessoaRepository;
    }

    private function emptyResponse(Response $response, int $statusCode): Response {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($statusCode);
    }

    private function validarCamposObrigatorios(array $data, array $requiredFields): bool {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    private function validarUUID(string $uuid): bool {
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid);
    }

    public function criarCandidatura(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return $this->emptyResponse($response, 400); // JSON inválido 
        }

        $requiredFields = ['id', 'id_vaga', 'id_pessoa'];
        if (!$this->validarCamposObrigatorios($data, $requiredFields)) {
            // Documento especifica 400 se não entendeu a requisição 
            return $this->emptyResponse($response, 400);
        }

        if (!$this->validarUUID($data['id']) ||
            !$this->validarUUID($data['id_vaga']) ||
            !$this->validarUUID($data['id_pessoa'])) {
            // Formato de UUID inválido é um Bad Request 
            return $this->emptyResponse($response, 400);
        }

        try {
            // Verificar se vaga e pessoa existem
            if (!$this->vagaRepository->findById($data['id_vaga']) || !$this->pessoaRepository->findById($data['id_pessoa'])) {
                // Se um dos dois não for encontrado, retorna 404 
                return $this->emptyResponse($response, 404);
            }

            // Verificar se o ID da candidatura já existe
            if ($this->candidaturaRepository->findById($data['id'])) {
                // Viola a regra de que o ID da candidatura deve ser único 
                return $this->emptyResponse($response, 400);
            }

            // Verificar duplicidade de candidatura (mesma pessoa para mesma vaga)
            if ($this->candidaturaRepository->findByVagaAndPessoa($data['id_vaga'], $data['id_pessoa'])) {
                // Viola a regra de que não pode haver duplicidade 
                return $this->emptyResponse($response, 400);
            }

            $candidatura = new Candidatura(
                $data['id'],
                $data['id_vaga'],
                $data['id_pessoa']
            );

            if ($this->candidaturaRepository->create($candidatura)) {
                return $this->emptyResponse($response, 201); // Sucesso 
            }
            
            return $this->emptyResponse($response, 500);

        } catch (PDOException $e) {
            error_log("Erro PDO ao criar candidatura: " . $e->getMessage());
            return $this->emptyResponse($response, 400);
        }
    }
}