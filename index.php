<?php

include_once('autoload.php');

$id = isset($_GET['id']) ? $_GET['id'] : '$id';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '$user_id';
$user = new User();
$admin = new Admin();
$file = new File();

// Эндпоинты:
$urlList = [
// Для класса User
    '/user' => ['GET' => ['User::list', $user], 'POST' => ['User::create', $user]],
    "/user?id=$id" => ['GET' => [$id, 'User::read', $user], 'DELETE' => [$id, 'User::delete', $user], 'PUT' => [$id, 'User::update', $user]],
    '/user/login' => ['POST' => ['User::login', $user]],    
    '/user/logout' => ['GET' => ['User::logout', $user]],
    '/user/reset_password' => ['POST' => ['User::reset_password', $user]],
// Для класса Admin
    '/admin/user' => ['GET' => ['Admin::list', $admin]],
    "/admin/user?id=$id" => ['GET' => [$id, 'Admin::read', $admin], 'DELETE' => [$id, 'Admin::delete', $admin], 'PUT' => [$id, 'Admin::update', $admin]],
// Для класса File
    '/file' => ['GET' => ['File::list', $file], 'POST' => ['File::create', $file]],
    "/file?id=$id" => ['GET' => [$id, 'File::read', $file], 'DELETE' => [$id, 'File::delete', $file], 'PUT' => [$id, 'File::update', $file]],
    "/file/share?id=$id" => ['GET' => [$id, 'File::readUsers', $file]],
    "/file/share?id=$id&user_id=$user_id" => ['DELETE' => [$id, $user_id, 'File::deleteUser', $file], 'PUT' => [$id, $user_id, 'File::addUser', $file]],
    '/directory' => ['POST' => ['File::createFolder', $file]],
    "/directory?id=$id" => ['GET' => [$id, 'File::readFolder', $file], 'DELETE' => [$id, 'File::deleteFolder', $file], 'PUT' => [$id, 'File::updateFolder', $file]]
];

// Роутинг:
foreach (array_keys($urlList) as $key) {
    if ($_SERVER['REQUEST_URI'] === "$key") {
        foreach ($urlList[$key] as $request => $funcData) {
            if ($_SERVER['REQUEST_METHOD'] === $request) {
                $obj = array_pop($funcData);
                $func = array_pop($funcData);
                if (count($funcData) == 2) {
                    call_user_func($func, $obj, $id, $user_id);
                }
                elseif (count($funcData) == 1) {
                    call_user_func($func, $obj, $id);
                } else {
                    call_user_func($func, $obj);
                }
            }
        }
    }
}
