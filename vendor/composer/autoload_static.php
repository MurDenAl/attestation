<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0bfa2f7073f1256acd9898cf384d9939
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0bfa2f7073f1256acd9898cf384d9939::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0bfa2f7073f1256acd9898cf384d9939::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0bfa2f7073f1256acd9898cf384d9939::$classMap;

        }, null, ClassLoader::class);
    }
}
