<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/utils/dependencies.php'; // Carregue suas dependências aqui

$app = AppFactory::create();

// Obtenha a conexão PDO
try {
    $pdo = getPDOConnection(); // Agora a função está disponível
} catch (\PDOException $e) {

    error_log("Erro de conexão PDO no index.php: " . $e->getMessage());
    // Você pode decidir encerrar a aplicação ou tentar lidar com isso de outra forma
    die("Erro ao conectar ao banco de dados. Verifique os logs.");
}

$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Passe a conexão PDO para seus arquivos de rotas
$vagasRoutes = require __DIR__ . '/../src/Routes/vagasRoutes.php';
$vagasRoutes($app, $pdo); // Passe $pdo para o closure das rotas

$app->get('/', function (Request $request, Response $response) {
    $payload = json_encode(['message' => 'API de Vagas está no ar! Acesse os endpoints corretos.', 'endpoints' => ['/vagas (POST)', '/vagas/{id}/candidaturas/ranking (GET)']]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// $pessoasRoutes = require __DIR__ . '/../src/Routes/pessoasRoutes.php';
// $pessoasRoutes($app, $pdo); // Passe $pdo também

// $candidaturasRoutes = require __DIR__ . '/../src/Routes/candidaturasRoutes.php';
// $candidaturasRoutes($app, $pdo); // Passe $pdo também

$app->run();