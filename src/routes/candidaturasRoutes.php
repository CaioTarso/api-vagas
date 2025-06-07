<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\CandidaturaController;
use App\Repositories\CandidaturaRepository;
use App\Repositories\VagaRepository;
use App\Repositories\PessoaRepository;

return function (App $app, PDO $pdo) {
    // Instancia todos os repositórios que o CandidaturaController precisa
    $candidaturaRepository = new CandidaturaRepository($pdo);
    $vagaRepository = new VagaRepository($pdo);
    $pessoaRepository = new PessoaRepository($pdo);
    
    // Instancia o controller, passando suas dependências
    $candidaturaController = new CandidaturaController(
        $candidaturaRepository,
        $vagaRepository,
        $pessoaRepository
    );

    // Define a rota POST /candidaturas
    $app->post('/candidaturas', function (Request $request, Response $response) use ($candidaturaController) {
        return $candidaturaController->criarCandidatura($request, $response);
    });
};