<?php

class User {
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
        $this->role = 'user';
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

// Проверка пользователя на права администратора
private function checkRole()
{
    if (isset($_SESSION['role'])) {
        if ($this->role !== $_SESSION['role']) {
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

// Авторизация пользователя
    public static function login($obj)
    {
        $statement = $obj->connection->prepare('SELECT * FROM user WHERE email = :email AND password = :password');
        $statement->bindValue('email', $_POST['email']);
        $statement->bindValue('password', $_POST['password']);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $userData = $statement->fetchAll();
            $_SESSION['id'] = $userData[0][0];
            $_SESSION['role'] = $userData[0][3];
            echo 'Вы успешно авторизированы!';
        } else {
            echo 'Неверный логин или пароль!';
            http_response_code(401);
        }
    }

// Выход из учетной записи
    public static function logout($obj)
    {
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
