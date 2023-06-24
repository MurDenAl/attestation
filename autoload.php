<?php

require_once('./vendor/autoload.php');

function loaderEntities($className):void
    {
        if(file_exists('entities/' . $className . '.php')) {
            require_once 'entities/' . $className . '.php';
        }
    }

spl_autoload_register('loaderEntities');
