<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\PessoaRepository;
use App\models\Pessoa;
use PDOException;

class PessoaController {
    private PessoaRepository $pessoaRepository;

    public function __construct(PessoaRepository $pessoaRepository) {
        $this->pessoaRepository = $pessoaRepository;
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

    private function validarLocalizacao(string $localizacao): bool {
        return in_array($localizacao, ['A', 'B', 'C', 'D', 'E', 'F']);
    }

    private function validarNivel($nivel): bool {
        return is_numeric($nivel) && (int)$nivel >= 1 && (int)$nivel <= 5;
    }

    private function validarUUID(string $uuid): bool {
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid);
    }

    public function criarPessoa(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return $this->emptyResponse($response, 400);
        }

        $requiredFields = ['id', 'nome', 'profissao', 'localizacao', 'nivel'];
        if (!$this->validarCamposObrigatorios($data, $requiredFields)) {
            return $this->emptyResponse($response, 422);
        }

        if (!$this->validarUUID($data['id']) ||
            !$this->validarLocalizacao($data['localizacao']) ||
            !$this->validarNivel(isset($data['nivel']) ? filter_var($data['nivel'], FILTER_VALIDATE_INT) : null)) {
            return $this->emptyResponse($response, 422);
        }
        $nivel = (int)$data['nivel'];

        try {
            if ($this->pessoaRepository->findById($data['id'])) {
                return $this->emptyResponse($response, 422); // ID já existe
            }

            $pessoa = new Pessoa(
                $data['id'],
                $data['nome'],
                $data['profissao'],
                $data['localizacao'],
                $nivel
            );

            if ($this->pessoaRepository->create($pessoa)) {
                return $this->emptyResponse($response, 201);
            }
            
            return $this->emptyResponse($response, 500);

        } catch (PDOException $e) {
            error_log("Erro PDO ao criar pessoa: " . $e->getMessage());
            return $this->emptyResponse($response, 422);
        }
    }
}