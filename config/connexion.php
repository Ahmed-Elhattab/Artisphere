<?php
function getPDO(): PDO
{
    
    //Recupération des informations de connexion à la BD
    $env = parse_ini_file(__DIR__ . '/.env');

    $db_host = $env['DB_HOST'] ?? 'localhost';
    $db_name = $env['DB_NAME'] ?? '';
    $db_user = $env['DB_USER'] ?? '';
    $db_pass = $env['DB_PASS'] ?? '';

    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";

    //connexion à la BD (une seule instance grâce à static)
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}
