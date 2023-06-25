<?php

class Admin {
    public PDO $connection;

// Подключение к базе данных:
    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=1111;charset=utf8", 'root', '');
        } catch (PDOException $exception) {
            echo json_encode($exception->getMessage());
        }
    }

// Получение списка пользователей
    public static function list($obj)
    {
        $statement = $obj->connection->query('SELECT * FROM user');
        $statement->execute();
        while ($data = $statement->fetchColumn(1)) {
            echo $data . PHP_EOL;
        }
    }

// Получение JSON конкретного пользователя
    public static function read($obj, $id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM user WHERE id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
        $data = $statement->fetchAll();
        echo json_encode($data);
    }

// Обновление данных пользователя
    public static function update($obj, $id)
    {
        parse_str(file_get_contents("php://input"), $PUT);
        if (isset($PUT['email'])) {
            $statement = $obj->connection->prepare("UPDATE user SET email = :email WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('email', $PUT['email']);
            $statement->execute();
        }
        if (isset($PUT['password'])) {
            $statement = $obj->connection->prepare("UPDATE user SET password = :password WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('password', $PUT['password']);
            $statement->execute();
        }
        if (isset($PUT['role'])) {
            $statement = $obj->connection->prepare("UPDATE user SET role = :role WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('role', $PUT['role']);
            $statement->execute();
        }
    }

// Удаление пользователя
    public static function delete($obj, $id)
    {
        $statement = $obj->connection->prepare('DELETE FROM user WHERE id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
    }
}