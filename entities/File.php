<?php

class File {
    private $root = 'C:/php/xampp/htdocs/userFiles';

// Создание файла
    public static function create($obj)
    {
        if (empty($_FILES)) {
            echo 'Файл для загрузки не выбран!';  
        } else {
            $id = 1;
            $searchResult = [];
            $obj->findFiles($obj->root, $searchResult);
            if ($searchResult === array()) {
                move_uploaded_file($_FILES['userFile']['tmp_name'], "$obj->root/" . "$id " . $_FILES['userFile']['name']);
            } else {
                foreach ($searchResult as $fileName) {
                    for (; str_starts_with($fileName, $id); $id++);
                }
                move_uploaded_file($_FILES['userFile']['tmp_name'], "$obj->root/" . "$id " . $_FILES['userFile']['name']);
            }
        }
    }

// Получение списка файлов
    public static function list($obj)
    {
        $searchResult = [];
        $obj->findFiles($obj->root, $searchResult);
        if ($searchResult === array()) {
            echo 'Нет сохраненных файлов.';
        } else {
            print_r($searchResult);
        }
    }

// Получение информации о конкретном файле
    public static function read($obj, $id)
    {
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
                echo $searchResult[$success];
            } else {
                echo 'Такого файла нет в хранилище.';
            }
        }
    }

// Перемещение или переименование файла
    public static function update($obj, $id)
    {
        //parse_str(file_get_contents("php://input"), $PUT);
        //print_r($PUT);
        //if (isset($PUT['newFileName'])) {
        //    $searchResult = [];
        //    $obj->findFiles($obj->root, $searchResult);
        //    if ($searchResult === array()) {
        //        echo 'Нет сохраненных файлов.';
        //    } else {
        //        $success = -1;
        //        foreach ($searchResult as $key => $fileName) {
        //            if (str_starts_with($fileName, $id)) {
        //                $success = $key;
        //            }
        //        }
        //        if ($success > -1) {
        //            rename("$obj->root/" . $searchResult[$success], "$obj->root/" . "$id " . $PUT['newFileName']);
        //        } else {
        //            echo 'Такого файла нет в хранилище.';
        //        }
        //    }
        //}
        //if (isset($PUT['newFileRoot'])) {
        //    $searchResult = [];
        //    $obj->findFiles($obj->root, $searchResult);
        //    if ($searchResult === array()) {
        //        echo 'Нет сохраненных файлов.';
        //    } else {
        //        $success = 0;
        //        foreach ($searchResult as $fileName) {
        //            if (str_starts_with($fileName, $id)) {
        //                $success = 1;
        //            }
        //        }
        //        if ($success == 1) {
        //            echo "$obj->root/" . $PUT['newFileRoot'] . "/$fileName";
        ////            rename("$obj->root/$fileName", "$obj->root/" . $PUT['newFileRoot'] . "/$fileName");
        //        } else {
        //            echo 'Такого файла нет в хранилище.';
        //        }
        //    }
        //}
    }

// Удаление файла
    public static function delete($obj, $id)
    {
        ;
    }

// Создание директории
    public static function createFolder($obj)
    {
        ;
    }

// Получение списка файлов директории
    public static function readFolder($obj, $id)
    {
        ;
    }

// Переименование директории
    public static function updateFolder($obj, $id)
    {
        ;
    }

// Удаление директории
    public static function deleteFolder($obj, $id)
    {
        ;
    }

// Получение списка пользователей, имеющих доступ к файлу
    public static function readUsers($obj, $id)
    {
        ;
    }

// Добавление доступа к файлу для пользователя
    public static function addUser($obj, $id, $user_id)
    {
        ;
    }

// Удаление доступа к файлу для пользователя
    public static function deleteUser($obj, $id, $user_id)
    {
        ;
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
            }
            elseif (is_dir("$root/$value")) {
                $this->findFiles("$root/$value", $searchResult);
            }
        }
    }
}
