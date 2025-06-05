<?php 
  function getPDOConnection(): PDO {
      static $pdo = null;
      if ($pdo === null) {
        $host = '127.0.0.1';
        $db = 'PDO';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
                try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            error_log("Erro de conexão com o banco: " . $e->getMessage());
            // Para a API, é melhor deixar o Slim tratar o erro e retornar um 500,
            // ou você pode customizar a resposta de erro aqui se preferir.
            // Lançar a exceção permite que o ErrorMiddleware do Slim a capture.
            throw $e; // Relança a exceção para o error handler do Slim
        }
    }
    return $pdo;
}


        
