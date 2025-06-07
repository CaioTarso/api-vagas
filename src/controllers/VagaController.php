<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utils\DistanceCalculator;
use App\Repositories\VagaRepository;
use App\models\Vaga;
use PDOException;

class VagaController {
    private VagaRepository $vagaRepository;
    private DistanceCalculator $distanceCalculator;

    public function __construct(
        VagaRepository $vagaRepository,
        DistanceCalculator $distanceCalculator
    ) {
        $this->vagaRepository = $vagaRepository;
        $this->distanceCalculator = $distanceCalculator;
    }

    private function jsonResponse(Response $response, $data, int $statusCode = 200): Response {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($statusCode);
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

    public function criarVaga(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            return $this->emptyResponse($response, 400);
        }

        $requiredFields = ['id', 'empresa', 'titulo', 'localizacao', 'nivel'];
        if (!$this->validarCamposObrigatorios($data, $requiredFields)) {
            return $this->emptyResponse($response, 422);
        }

        if (!$this->validarUUID($data['id']) ||
            !$this->validarLocalizacao($data['localizacao']) ||
            !$this->validarNivel(isset($data['nivel']) ? filter_var($data['nivel'], FILTER_VALIDATE_INT) : null)) {
            return $this->emptyResponse($response, 422);
        }
        
        // Nível já validado como int e no range
        $nivel = (int)$data['nivel']; 

        try {
            if ($this->vagaRepository->findById($data['id'])) {
                return $this->emptyResponse($response, 422);
            }

            $vaga = new Vaga(
                $data['id'],
                $data['empresa'],
                $data['titulo'],
                $data['localizacao'],
                $nivel,
                $data['descricao'] ?? null
            );

            if ($this->vagaRepository->create($vaga)) {
                return $this->emptyResponse($response, 201);
            }
            
            return $this->emptyResponse($response, 500);

        } catch (PDOException $e) {
            error_log("Erro PDO ao criar vaga: " . $e->getMessage());
            return $this->emptyResponse($response, 422);
        } catch (\Exception $e) {
            error_log("Erro inesperado ao criar vaga: " . $e->getMessage());
            return $this->emptyResponse($response, 500);
        }
    }

    private function calcularN(int $vagaNivel, int $candidatoNivel): int {
        return 100 - (25 * abs($vagaNivel - $candidatoNivel));
    }

    private function calcularD(int $distancia): int {
        return match (true) {
            $distancia >= 0 && $distancia <= 5 => 100,
            $distancia > 5 && $distancia <= 10 => 75,
            $distancia > 10 && $distancia <= 15 => 50,
            $distancia > 15 && $distancia <= 20 => 25,
            default => 0
        };
    }

    private function calcularScore(int $n, int $d): int {
        return (int)(($n + $d) / 2);
    }

    public function rankingCandidaturas(Request $request, Response $response, array $args): Response {
        $id_vaga = $args['id'] ?? ($args['id_vaga'] ?? null);

        if (!$id_vaga || !$this->validarUUID($id_vaga)) {
             return $this->emptyResponse($response, 400);
        }

        $vagaDetails = $this->vagaRepository->getVagaDetailsForRanking($id_vaga);

        if (!$vagaDetails) {
            return $this->emptyResponse($response, 404);
        }

        $vagaLocalizacao = $vagaDetails['localizacao'];
        $vagaNivel = (int)$vagaDetails['nivel'];

        $candidatos = $this->vagaRepository->getCandidatosForRanking($id_vaga);
        $candidatosRanqueados = [];

        foreach ($candidatos as $candidato) {
            $distancia = $this->distanceCalculator->getShortestDistance(
                $vagaLocalizacao,
                $candidato['candidato_localizacao'] ?? ''
            );
            $distanciaCalculada = ($distancia === null) ? 999 : $distancia;

            $N = $this->calcularN($vagaNivel, (int)($candidato['candidato_nivel'] ?? 0));
            $D = $this->calcularD($distanciaCalculada);
            $score = $this->calcularScore($N, $D);

            $candidatosRanqueados[] = [
                'nome' => $candidato['nome'],
                'profissao' => $candidato['profissao'],
                'localizacao' => $candidato['candidato_localizacao'],
                'nivel' => (int)($candidato['candidato_nivel'] ?? 0),
                'score' => $score
            ];
        }

        usort($candidatosRanqueados, fn($a, $b) => $b['score'] <=> $a['score']);

        return $this->jsonResponse($response, $candidatosRanqueados, 200);
    }
}