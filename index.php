<?php

include_once('autoload.php');

$urlList = [
    '/user/' => ['GET' => 'User::list()', 'POST' => 'User::create()', 'PUT' => 'User::update($id)'],
    '/user/{id}' => ['GET' => 'User::read($id)', 'DELETE' => 'User::delete($id)'],
];

switch ($_SERVER['REDIRECT_URL']) {
    case '/user' :
        $controller = new User();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST' :
                $controller->create($_POST['email'], $_POST['password'], $_POST['role']);
                break;
            case 'GET' :
                if (isset($_GET['id'])) {
                    $controller->read($_GET['id']);
                } else {
                    $controller->list();
                }
                break;
            case 'PUT' :
                $controller->update($_GET['id']);
                break;
            case 'DELETE' :
                $controller->delete($_GET['id']);
                break;
        }
        break;
}
