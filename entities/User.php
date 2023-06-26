<?php

class User {
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

// Добавление пользователя
    public static function create($obj)
    {
        $statement = $obj->connection->prepare("INSERT INTO user(id, email, password, role) values(null, :email, :password, :role)");
        $statement->bindValue('email', $_POST['email']);
        $statement->bindValue('password', $_POST['password']);
        $statement->bindValue('role', $_POST['role']);
        $statement->execute();
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

// Авторизация пользователя
    public static function login($obj)
    {
        $statement = $obj->connection->prepare('SELECT * FROM user WHERE email = :email AND password = :password');
        $statement->bindValue('email', $_POST['email']);
        $statement->bindValue('password', $_POST['password']);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            session_start();
            $_SESSION['id'] = session_id();
            $_SESSION['role'] = $statement->fetchColumn(3);
            echo 'Вы успешно авторизированы!';
        } else {
            echo 'Неверный логин или пароль!';
            http_response_code(401);
        }
    }

// Выход из учетной записи
    public static function logout($obj)
    {
        session_start();
        unset($_SESSION['id']);
        unset($_SESSION['role']);
    }

// Сброс пароля
    public static function reset_password($obj)
    {
        include_once('MailException.php');
        try {
            echo MailException::checkMail();
        } catch (MailException $exception) {
            echo $exception->getMessage();
        }
    }
}
