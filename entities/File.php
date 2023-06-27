<?php

class File {
    public PDO $connection; 
    private $root = 'C:/php/xampp/htdocs/fileStorage';

// Подключение к базе данных:
public function __construct()
{
    try {
        $this->connection = new PDO("mysql:host=localhost;dbname=1111;charset=utf8", 'root', '');
    } catch (PDOException $exception) {
        echo json_encode($exception->getMessage());
    }
}

// Создание файла
    public static function create($obj)
    {
        if (!isset($_SESSION['id'])) {
            echo 'Вход в аккаунт не выполнен!';
            die(http_response_code(401));
        }
        if (empty($_FILES)) {
            echo 'Файл для загрузки не выбран!';  
        } else {
            $id = 1;
            $searchResult = [];
            $obj->findFiles($obj->root, $searchResult);
            if ($searchResult === array()) {
                move_uploaded_file($_FILES['userFile']['tmp_name'], "$obj->root/" . "$id " . $_FILES['userFile']['name']);
                $statement = $obj->connection->prepare("INSERT INTO relations(id, user_id, file_id) values(null, :user_id, :file_id)");
                $statement->bindValue('user_id', $_SESSION['id']);
                $statement->bindValue('file_id', $id);
                $statement->execute();
            } else {
                foreach ($searchResult as $fileName) {
                    for (; str_starts_with($fileName, $id); $id++);
                }
                move_uploaded_file($_FILES['userFile']['tmp_name'], "$obj->root/" . "$id " . $_FILES['userFile']['name']);
                $statement = $obj->connection->prepare("INSERT INTO relations(id, user_id, file_id) values(null, :user_id, :file_id)");
                $statement->bindValue('user_id', $_SESSION['id']);
                $statement->bindValue('file_id', $id);
                $statement->execute();
            }
        }
    }

// Получение списка файлов
    public static function list($obj)
    {
        if (!isset($_SESSION['id'])) {
            echo 'Вход в аккаунт не выполнен!';
            die(http_response_code(401));
        }
        $searchResult = [];
        $obj->findFiles($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Нет сохраненных файлов.';
        } else {
            foreach ($searchResult as $key => $value) {
                if ($key % 3 == 0) {
                    echo $value . PHP_EOL;
                }
            }
        }
    }

// Получение информации о конкретном файле
    public static function read($obj, $id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        $searchResult = [];
        $obj->findFiles($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Нет сохраненных файлов.';
        } else {
            $success = -1;
            foreach ($searchResult as $key => $fileName) {
                if (str_starts_with($fileName, $id)) {
                    $success = $key;
                }
            }
            if ($success > -1) {
                echo $searchResult[$success] . PHP_EOL . $searchResult[$success + 1] . PHP_EOL . $searchResult[$success + 2];
            } else {
                echo 'Такого файла нет в хранилище.';
            }
        }
    }

// Перемещение или переименование файла
    public static function update($obj, $id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        parse_str(file_get_contents("php://input"), $PUT);
        if (isset($PUT['newFileName'])) {
            $searchResult = [];
            $obj->findFiles($obj->root, $searchResult);
            if ($searchResult === array()) {
                echo 'Нет сохраненных файлов.';
            } else {
                $success = -1;
                foreach ($searchResult as $key => $fileName) {
                    if (str_starts_with($fileName, $id)) {
                        $success = $key;
                    }
                }
                if ($success > -1) {
                    rename($searchResult[$success+1] . $searchResult[$success], $searchResult[$success+1] . "$id " . $PUT['newFileName']);
                } else {
                    echo 'Такого файла нет в хранилище.';
                }
            }
        }
        if (isset($PUT['newFileRoot'])) {
            $searchResult = [];
            $obj->findFiles($obj->root, $searchResult);
            if ($searchResult === array()) {
                echo 'Нет сохраненных файлов.';
            } else {
                $success = -1;
                foreach ($searchResult as $key => $fileName) {
                    if (str_starts_with($fileName, $id)) {
                        $success = $key;
                    }
                }
                if ($success > -1) {
                    rename($searchResult[$success+1] . $searchResult[$success], "$obj->root/" . $PUT['newFileRoot'] . "/$searchResult[$success]");
                } else {
                    echo 'Такого файла нет в хранилище.';
                }
            }
        }
    }

// Удаление файла
    public static function delete($obj, $id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        $searchResult = [];
        $obj->findFiles($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Нет сохраненных файлов.';
        } else {
            $success = -1;
            foreach ($searchResult as $key => $fileName) {
                if (str_starts_with($fileName, $id)) {
                    $success = $key;
                }
            }
            if ($success > -1) {
                unlink($searchResult[$success+1] . $searchResult[$success]);
                echo 'Файл успешно удален!';
            } else {
                echo 'Такого файла нет в хранилище.';
            }
        }
    }

// Создание директории
    public static function createFolder($obj)
    {
        if (!isset($_SESSION['id'])) {
            echo 'Вход в аккаунт не выполнен!';
            die(http_response_code(401));
        }
        $id = 1;
        $searchResult = [];
        $obj->findDirs($obj->root, $searchResult);
        if ($searchResult === array()) {
            mkdir("$obj->root/" . $_POST['newFolder'] . "_$id");
        } else {
            foreach ($searchResult as $dirName) {
                for (; str_ends_with($dirName, $id); $id++);
            }
            print_r($searchResult);
            mkdir("$obj->root/" . $_POST['newFolder'] . "_$id");
        }
    }

// Получение списка файлов директории
    public static function readFolder($obj, $id)
    {
        $searchResult = [];
        $obj->findDirs($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Отсутствуют директории в хранилище.';
        } else {
            $success = -1;
            foreach ($searchResult as $key => $dirName) {
                if (str_ends_with($dirName, $id)) {
                    $success = $key;
                }
            }
            if ($success > -1) {
                $newSearchResult = [];
                $obj->findFiles("$obj->root/$searchResult[$success]", $newSearchResult);
                if ($newSearchResult === array()) {
                    echo 'Нет сохраненных файлов.';
                } else {
                    foreach ($newSearchResult as $key => $value) {
                        if ($key % 3 == 0) {
                            echo $value . PHP_EOL;
                        }
                    }
                }
            } else {
                echo 'Такой директории нет в хранилище.';
            }
        }
    }

// Переименование директории
    public static function updateFolder($obj, $id)
    {
        parse_str(file_get_contents("php://input"), $PUT);
        if (isset($PUT['newFolderName'])) {
            $searchResult = [];
            $obj->findDirs($obj->root, $searchResult);
            if ($searchResult === array()) {
                echo 'Отсутствуют директории в хранилище.';
            } else {
                $success = -1;
                foreach ($searchResult as $key => $dirName) {
                    if (str_ends_with($dirName, $id)) {
                        $success = $key;
                    }
                }
                if ($success > -1) {
                    rename("$obj->root/$searchResult[$success]", "$obj->root/" . $PUT['newFolderName'] . "_$id");
                } else {
                    echo 'Такой директории нет в хранилище.';
                }
            }
        }
    }

// Удаление директории
    public static function deleteFolder($obj, $id)
    {
        $searchResult = [];
        $obj->findDirs($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Отсутствуют директории в хранилище.';
        } else {
            $success = -1;
            foreach ($searchResult as $key => $dirName) {
                if (str_ends_with($dirName, $id)) {
                    $success = $key;
                }
            }
            if ($success > -1) {
                rmdir("$obj->root/$searchResult[$success]");
            } else {
                echo 'Такой директории нет в хранилище.';
            }
        }
    }

// Получение списка пользователей, имеющих доступ к файлу
    public static function readUsers($obj, $id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE file_id = :file_id');
        $statement->bindValue('file_id', $id);
        $statement->execute();
        while ($data = $statement->fetchColumn(1)) {
            $stmt = $obj->connection->prepare('SELECT * FROM user WHERE id = :id');
            $stmt->bindValue('id', $data);
            $stmt->execute();
            echo $stmt->fetchColumn(1) . PHP_EOL;
        }
    }

// Добавление доступа к файлу для пользователя
    public static function addUser($obj, $id, $user_id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        $statement = $obj->connection->prepare("INSERT INTO relations(id, user_id, file_id) values(null, :user_id, :file_id)");
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('file_id', $id);
        $statement->execute();
    }

// Удаление доступа к файлу для пользователя
    public static function deleteUser($obj, $id, $user_id)
    {
        $statement = $obj->connection->prepare('SELECT * FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $_SESSION['id']);
        $statement->bindValue('file_id', $id);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            echo 'Нет прав для выполнения данного действия!';
            die(http_response_code(403));
        }
        $statement = $obj->connection->prepare('DELETE FROM relations WHERE user_id = :user_id AND file_id = :file_id');
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('file_id', $id);
        $statement->execute();
    }

// Рекурсивный поиск файлов в хранилище
    private function findFiles($root, &$searchResult)
    {
        foreach (scandir($root)  as $value) {
            if ($value == '.' || $value == '..') {
                continue;
            }
            elseif (is_file("$root/$value")) {
                $searchResult[] = "$value";
                $searchResult[] = "$root/";
                $searchResult[] = filesize("$root/$value") . ' bytes';
            }
            elseif (is_dir("$root/$value")) {
                $this->findFiles("$root/$value", $searchResult);
            }
        }
    }

// Рекурсивный поиск папок в хранилище
    private function findDirs($root, &$searchResult)
    {
        foreach (scandir($root)  as $value) {
            if ($value == '.' || $value == '..') {
                continue;
            }
            elseif (is_dir("$root/$value")) {
                $searchResult[] = "$value";
                $this->findDirs("$root/$value", $searchResult);
            }
        }
    }
}
