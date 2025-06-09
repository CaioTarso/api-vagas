<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\controllers\CandidaturaController;
use App\Repositories\CandidaturaRepository;
use App\Repositories\PessoaRepository;
use App\Repositories\VagaRepository;

return function (App $app, PDO $pdo) {
    $candidaturaRepository = new CandidaturaRepository($pdo);
    $pessoaReository = new PessoaRepository($pdo);
    $vagaRepository = new VagaRepository($pdo);
    $candidaturaController = new CandidaturaController(
        $candidaturaRepository,
        $pessoaReository, 
        $vagaRepository);

    $app->post('/candidaturas', function (Request $request, Response $response) use ($candidaturaController) {
        return $candidaturaController->criarCandidatura($request, $response);
    });
};