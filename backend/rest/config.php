<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config
{
    public static function DB_NAME()
    {
        return 'car_rental_db';
    }
    public static function DB_PORT()
    {
        return  3306;
    }
    public static function DB_USER()
    {
        return 'root';
    }
    public static function DB_PASSWORD()
    {
        return '';
    }
    public static function DB_HOST()
    {
        return '127.0.0.1';
    }

    public static function JWT_SECRET()
    {
        return '6aebb34d6a169bacb2394acad96b41318bb3376e8b151dd35388e63e5092d0d6';
    }
}

class Database {
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    Config::DB_HOST(),
                    Config::DB_PORT(),
                    Config::DB_NAME()
                );

                self::$connection = new PDO(
                    $dsn,
                    Config::DB_USER(),
                    Config::DB_PASSWORD(),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$connection;
    }
}

?>
