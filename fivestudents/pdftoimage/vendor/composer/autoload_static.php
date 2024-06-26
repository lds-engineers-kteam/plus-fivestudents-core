<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6e0540fe573461aa91cfce1a59e378b8
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\Stdlib\\' => 12,
            'Zend\\Memory\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\Stdlib\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-stdlib/src',
        ),
        'Zend\\Memory\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-memory/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'Z' => 
        array (
            'ZendPdf\\' => 
            array (
                0 => __DIR__ . '/..' . '/zendframework/zendpdf/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6e0540fe573461aa91cfce1a59e378b8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6e0540fe573461aa91cfce1a59e378b8::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit6e0540fe573461aa91cfce1a59e378b8::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
