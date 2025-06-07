<?php

class Database {
    // Atributo privado e estático que vai guardar a instância
    private static ?PDO $instance = null;

    // Construtor privado impede instâncias externas com `new`
    private function __construct() {}

    // Método público e estático que retorna a instância
    public static function getConnection(): PDO {
        // Se ainda não existe, cria a conexão
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db = 'api_vagas';
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
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                error_log("Erro de conexão com o banco: " . $e->getMessage());
                throw $e;
            }
        }

        // Retorna a mesma instância já criada
        return self::$instance;
    }
}
