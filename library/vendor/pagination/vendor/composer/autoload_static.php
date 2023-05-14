<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7118704d15a4b553d0e177cd4d64dc91
{
    public static $prefixLengthsPsr4 = array (
        'y' => 
        array (
            'yidas\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'yidas\\' => 
        array (
            0 => __DIR__ . '/..' . '/yidas/pagination/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7118704d15a4b553d0e177cd4d64dc91::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7118704d15a4b553d0e177cd4d64dc91::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7118704d15a4b553d0e177cd4d64dc91::$classMap;

        }, null, ClassLoader::class);
    }
}
