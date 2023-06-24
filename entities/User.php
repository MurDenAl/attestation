<?php

class User {
    protected PDO $connection;

    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=localhost;dbname=1111;charset=utf8", 'root', '');
        } catch (PDOException $exception) {
            echo json_encode($exception->getMessage());
        }
    }

    public function create($email, $password, $role)
    {
        $statement = $this->connection->prepare("INSERT INTO user(id, email, password, role) values(null, :email, :password, :role)");
        $statement->bindValue('email', $email);
        $statement->bindValue('password', $password);
        $statement->bindValue('role', $role);
        $statement->execute();
    }

    public function list()
    {
        $statement = $this->connection->query('SELECT * FROM user');
        $statement->execute();
        while ($data = $statement->fetchColumn(1)) {
            echo $data . PHP_EOL;
        }
    }

    public function read($id)
    {
        $statement = $this->connection->prepare('SELECT * FROM user WHERE id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
        $data = $statement->fetchAll();
        echo json_encode($data);
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"), $PUT);
        if (isset($PUT['email'])) {
            $statement = $this->connection->prepare("UPDATE user SET email = :email WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('email', $PUT['email']);
            $statement->execute();
        }
        if (isset($PUT['password'])) {
            $statement = $this->connection->prepare("UPDATE user SET password = :password WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('password', $PUT['password']);
            $statement->execute();
        }
        if (isset($PUT['role'])) {
            $statement = $this->connection->prepare("UPDATE user SET role = :role WHERE id = :id");
            $statement->bindValue('id', $id);
            $statement->bindValue('role', $PUT['role']);
            $statement->execute();
        }
    }

    public function delete($id)
    {
        $statement = $this->connection->prepare('DELETE FROM user WHERE id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
    }
}
