<?php

namespace Cavesman;

class FileSystem
{
    public static function srcDir(): string
    {
        return self::documentRoot() . '/src';
    }
    public static function appDir(): string
    {
        return self::documentRoot() . '/app';
    }
    public static function publicDir(): string
    {
        return self::documentRoot() . '/public';
    }
    public static function viewsDir(): string
    {
        return self::srcDir() . '/views';
    }

    public static function getPath($relativePath = ''): string {
        if(!file_exists(self::documentRoot() . $relativePath))
            return self::documentRoot() . $relativePath;
    }

    public static function documentRoot()
    {
        //config dir of app
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/../vendor"))
            return $_SERVER['DOCUMENT_ROOT'] . "/..";
        elseif (is_dir($_SERVER['DOCUMENT_ROOT'] . "/vendor"))
            return $_SERVER['DOCUMENT_ROOT'] . "/vendor";
        elseif (!empty($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/../vendor"))
            return $_SERVER['PWD'] . "/..";
        elseif (!empty($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/vendor"))
            return $_SERVER['PWD'];
        elseif (is_dir(getcwd() . "/../vendor"))
            return getcwd() . "/..";
        elseif (is_dir(getcwd() . "/vendor"))
            return getcwd();

        return $_SERVER['DOCUMENT_ROOT'];
    }
}
