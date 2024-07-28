<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit60b450f1c81d38d73870659596c4296b
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Smashballoon\\Stubs\\' => 19,
            'Smashballoon\\Customizer\\V3\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Smashballoon\\Stubs\\' => 
        array (
            0 => __DIR__ . '/..' . '/smashballoon/stubs/src',
        ),
        'Smashballoon\\Customizer\\V3\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/V3',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit60b450f1c81d38d73870659596c4296b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit60b450f1c81d38d73870659596c4296b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit60b450f1c81d38d73870659596c4296b::$classMap;

        }, null, ClassLoader::class);
    }
}