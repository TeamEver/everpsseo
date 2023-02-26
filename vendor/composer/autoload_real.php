<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit95fd724e56001749f09acab5bd0bcfcf
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit95fd724e56001749f09acab5bd0bcfcf', 'loadClassLoader'), true, false);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit95fd724e56001749f09acab5bd0bcfcf', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit95fd724e56001749f09acab5bd0bcfcf::getInitializer($loader));

        $loader->register(false);

        return $loader;
    }
}
