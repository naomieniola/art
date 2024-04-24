<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf211d999b380a201ab7fd66565c870c7
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Snipe\\BanBuilder\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Snipe\\BanBuilder\\' => 
        array (
            0 => __DIR__ . '/..' . '/snipe/banbuilder/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf211d999b380a201ab7fd66565c870c7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf211d999b380a201ab7fd66565c870c7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf211d999b380a201ab7fd66565c870c7::$classMap;

        }, null, ClassLoader::class);
    }
}
