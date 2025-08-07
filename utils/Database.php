<?php

class Database
{
    /** @var PDO|null */
    private static $connection = null;

    public static function connect($host, $port, $username, $password, $dbname = null)
    {
        if (self::$connection === null) {
            try {
                $dsn = ['mysql:host=' . $host];
                $dsn[] = 'port=' . $port;
                if ($dbname) {
                    $dsn[] = 'dbname=' . $dbname;
                }
                $dsn[] = 'charset=utf8mb4';
                self::$connection = new PDO(
                    implode(';', $dsn),
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function query($sql)
    {
        if (self::$connection === null) {
            throw new Exception("No database connection established.");
        }
        return self::$connection->query($sql);
    }

    public static function close()
    {
        if (self::$connection !== null) {
            self::$connection = null;
        }
    }
}
