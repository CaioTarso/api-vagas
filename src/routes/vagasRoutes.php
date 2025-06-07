<?php
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\controllers\VagaController;
use App\Repositories\VagaRepository; 
use App\utils\DistanceCalculator;


// A função agora aceita $app e a conexão $pdo, que é passada pelo index.php
return function (App $app, PDO $pdo) {


    $distanceCalculator = new DistanceCalculator();
    $vagaRepository = new VagaRepository($pdo); // Instancia o repositório com a conexão PDO

    // Instancia o controller com VagaRepository e DistanceCalculator
    $vagaController = new VagaController($vagaRepository, $distanceCalculator);

    

    // Rotas
    $app->post('/vagas', function (Request $request, Response $response) use ($vagaController) {
        return $vagaController->criarVaga($request, $response);
    });

    $app->get('/vagas/{id}/candidaturas/ranking', function (Request $request, Response $response, array $args) use ($vagaController) {
        return $vagaController->rankingCandidaturas($request, $response, $args);
    });
    
};