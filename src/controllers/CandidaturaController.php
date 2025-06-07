<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\CandidaturaRepository;
use App\models\Candidatura;
use PDOException;

class CandidaturaController {
    private CandidaturaRepository $candidaturaRepository;

    public function __construct(CandidaturaRepository $candidaturaRepository) {
        $this->candidaturaRepository = $candidaturaRepository;
    }

    private function emptyResponse(Response $response, int $statusCode): Response {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($statusCode);
    }

    private function validarCamposObrigatorios(array $data, array $requiredFields): bool {
        foreach ($requiredFields as $field) {
            if (empty($data[$field]) && $data[$field] !== 0 && $data[$field] !== '0') {
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
            return $this->emptyResponse($response, 400);
        }

        $requiredFields = ['id', 'id_vaga', 'id_pessoa'];
        if (!$this->validarCamposObrigatorios($data, $requiredFields)) {
            return $this->emptyResponse($response, 422);
        }

        if (!$this->validarUUID($data['id']) || !$this->validarUUID($data['id_vaga']) || !$this->validarUUID($data['id_pessoa'])) {
            return $this->emptyResponse($response, 422);
        }

        try {
            $candidatura = new Candidatura(
                $data['id'],
                $data['id_vaga'],
                $data['id_pessoa']
            );

            if ($this->candidaturaRepository->create($candidatura)) {
                $response->getBody()->write(json_encode(['message' => 'Candidatura criada com sucesso']));
                return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(201);
            }

            return $this->emptyResponse($response, 500);
        } catch (PDOException $e) {
            return $this->emptyResponse($response, 500);
        }
    }
}