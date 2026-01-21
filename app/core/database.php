<?php
class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // On lit le .env à la racine (dossier SITE)
            // remettre si pas connecter hangar garage : $envPath = dirname(__DIR__, 2) . '/.env';
            //$env = parse_ini_file($envPath);

            $host = getenv('DB_HOST') ?? 'localhost';
            $name = getenv('DB_NAME') ?? '';
            $user = getenv('DB_USER') ?? '';
            $pass = getenv('DB_PASS') ?? '';

            $dsn = "mysql:host=$host;dbname=$name;charset=utf8";

            self::$pdo = new PDO($dsn, $user, $pass);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

    
}