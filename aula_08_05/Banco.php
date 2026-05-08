<?php

class Banco
{
    private static $conexao = null;

    public static function conexao()
    {
        if (self::$conexao !== null) {
            return self::$conexao;
        }

        $config = require __DIR__ . '/../../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$conexao = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            http_response_code(500);
            echo 'Erro de conexao com banco: ' . $exception->getMessage();
            exit;
        }

        return self::$conexao;
    }
}
