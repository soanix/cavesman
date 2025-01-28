<?php

namespace Cavesman;

class Fs
{
    const string SRC_DIR = _ROOT_ . '/src';
    const string APP_DIR = _ROOT_ . '/app';
    const string PUBLIC_DIR = _ROOT_ . '/public';
    const string VIEWS_DIR = _ROOT_ . '/src/Views';


    public static function getRootDir()
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