<?php

class Admin {
    public PDO $connection;
    private $role;

// Подключение к базе данных:
    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=1111;charset=utf8", 'root', '');
        } catch (PDOException $exception) {
            echo json_encode($exception->getMessage());
        }
        $this->role = 'admin';
    }

// Проверка пользователя на права администратора
    private function checkRole()
    {
        session_start();
        if (isset($_SESSION['role'])) {
            if ($this->role == $_SESSION['role']) {
                return true;
            } else {
                return false;
            }
        } else {
            echo 'Вход в аккаунт не выполнен!';
            die(http_response_code(401));
        }
    }

// Получение списка пользователей
    public static function list($obj)
    {
        if ($obj->checkRole() === true) {
            $statement = $obj->connection->query('SELECT * FROM user');
            $statement->execute();
            while ($data = $statement->fetchColumn(1)) {
            echo $data . PHP_EOL;
            }
        } else {
            echo 'Нет прав для выполнения данного действия!';
            http_response_code(403);
        }
    }

// Получение JSON конкретного пользователя
    public static function read($obj, $id)
    {
        if ($obj->checkRole() === true) {
            $statement = $obj->connection->prepare('SELECT * FROM user WHERE id = :id');
            $statement->bindValue('id', $id);
            $statement->execute();
            $data = $statement->fetchAll();
            echo json_encode($data);
        } else {
            echo 'Нет прав для выполнения данного действия!';
            http_response_code(403);
        }
    }

// Обновление данных пользователя
    public static function update($obj, $id)
    {
        if ($obj->checkRole() === true) {
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
        } else {
            echo 'Нет прав для выполнения данного действия!';
            http_response_code(403);
        }
    }

// Удаление пользователя
    public static function delete($obj, $id)
    {
        if ($obj->checkRole() === true) {
            $statement = $obj->connection->prepare('DELETE FROM user WHERE id = :id');
            $statement->bindValue('id', $id);
            $statement->execute();
        } else {
            echo 'Нет прав для выполнения данного действия!';
            http_response_code(403);
        }
    }
}
