<?php

namespace Cavesman;

use Cavesman\Enum\Directory;
use Cavesman\Twig\ConfigExtension;
use Cavesman\Twig\PathExtension;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\FilesystemLoader;


/**
 * Twig Class
 * <code>
 *     Cavesman\Twig::getInstance()
 * </code>
 */
class Twig
{
    public static ?Environment $instance = null;

    /**
     * Render twig template
     *
     * @param string $string
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws SyntaxError
     */
    public static function renderFromString(string $string, array $context = []): string
    {
        $template = self::getInstance()->createTemplate($string);
        return $template->render($context);
    }

    /**
     * Create Instance
     *
     * @return Environment
     */
    protected static function getInstance(): Environment
    {
        if ((self::$instance instanceof self) === false) {
            $loader = new FilesystemLoader([FileSystem::getPath(Directory::VIEWS) . '/' . Config::get('params.theme') . '/templates']);
            self::$instance = new Environment($loader);
            self::$instance->addExtension(new StringLoaderExtension());
            self::$instance->addExtension(new ConfigExtension());
            self::$instance->addExtension(new PathExtension());
        }
        return self::$instance;
    }

    /**
     * Render twig template
     *
     * @param $name
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function render($name, array $context = []): string
    {
        return self::getInstance()->render($name, $context);
    }
}

