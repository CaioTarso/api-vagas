<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/utils/dependencies.php'; 

$app = AppFactory::create();

// conexão PDO
try {
    $pdo = Database::getConnection();
} catch (\PDOException $e) {

    error_log("Erro de conexão PDO no index.php: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Verifique os logs.");
}

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$vagasRoutes = require __DIR__ . '/../src/routes/vagasRoutes.php';
$vagasRoutes($app, $pdo); 

$candidaturasRoutes = require __DIR__ . '/../src/routes/candidaturasRoutes.php';
$candidaturasRoutes($app, $pdo);

$pessoasRoutes = require __DIR__ . '/../src/routes/pessoasRoutes.php';
$pessoasRoutes($app, $pdo);

// $candidaturasRoutes = require __DIR__ . '/../src/Routes/candidaturasRoutes.php';
// $candidaturasRoutes($app, $pdo);


$app->get('/', function (Request $request, Response $response) {
    $payload = json_encode(['message' => 'API de Vagas está no ar! Acesse os endpoints corretos.', 'endpoints' => ['/vagas (POST)', '/vagas/{id}/candidaturas/ranking (GET)' , '/pessoas (POST)']]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->run();