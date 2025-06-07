<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\controllers\CandidaturaController;
use App\Repositories\CandidaturaRepository;

return function (App $app, PDO $pdo) {
    $candidaturaRepository = new CandidaturaRepository($pdo);
    $candidaturaController = new CandidaturaController($candidaturaRepository);

    $app->post('/candidaturas', function (Request $request, Response $response) use ($candidaturaController) {
        return $candidaturaController->criarCandidatura($request, $response);
    });
};