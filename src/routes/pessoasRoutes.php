<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\PessoaController;
use App\Repositories\PessoaRepository;

return function (App $app, PDO $pdo) {
    $pessoaRepository = new PessoaRepository($pdo);
    $pessoaController = new PessoaController($pessoaRepository);

    $app->post('/pessoas', function (Request $request, Response $response) use ($pessoaController) {
        return $pessoaController->criarPessoa($request, $response);
    });
};