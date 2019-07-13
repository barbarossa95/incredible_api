<?php

/**
 * Класс-синглтон для получения pdo соединения с бд
 */
class DB
{
    private static $instance;

    public static function getInstance()
    {
        // инициализация подключения если его еще нет
        if (empty(self::$instance)) {

            // Стандартные credentials
            // Следует получать их из переменных среды, но для примера захардкожено
            $db_info = [
                "db_host" => "localhost",
                "db_port" => "3306",
                "db_user" => "root",
                "db_pass" => "",
                "db_name" => "incredible_db",
                "db_charset" => "UTF-8"
            ];

            try {
                self::$instance = new PDO(
                    "mysql:host=" . $db_info['db_host']
                        . ';port=' . $db_info['db_port']
                        . ';dbname=' . $db_info['db_name'],
                    $db_info['db_user'],
                    $db_info['db_pass']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                self::$instance->query('SET NAMES utf8');
                self::$instance->query('SET CHARACTER SET utf8');

                return self::$instance;
            } catch (PDOException $error) {
                echo $error->getMessage();
            }
        }

        return self::$instance;
    }
}
