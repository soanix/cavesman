<?php

namespace Cavesman;

use Cavesman\Enum\Directory;

/**
 * Class FileSystem
 *
 * Provides utilities for managing and resolving file system paths within the application.
 */
class FileSystem
{
    /**
     * Resolves the absolute path for a given directory or custom path.
     *
     * This method supports both enum values (from `Directory`) and plain string paths.
     * If the provided name matches a case in the `Directory` enum, its value is used.
     * Otherwise, it treats the input as a relative path.
     *
     * @param string|Directory $name The directory name (enum case or string path). Defaults to an empty string.
     * @return string The resolved absolute path.
     */
    public static function getPath(string|Directory $name = ''): string
    {
        if ($name instanceof Directory) {
            // If $name is an instance of the Directory enum, return its value.
            return self::documentRoot() . $name->value;
        }

        // If $name is a string and matches a Directory enum case, use the case's value.
        if ($enum = Directory::tryFromName($name)) {
            return self::documentRoot() . $enum->value;
        }

        // Otherwise, treat $name as a relative path and resolve it.
        return self::documentRoot() . $name;
    }

    /**
     * Determines the document root of the application.
     *
     * This method attempts to locate the root directory by checking common paths where
     * the `vendor` directory might be located, based on the server's configuration or the current working directory.
     *
     * @return string The absolute path to the document root.
     */
    public static function documentRoot(): string
    {
        // Check for the vendor directory in various common locations.
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/../vendor")) {
            return $_SERVER['DOCUMENT_ROOT'] . "/..";
        } elseif (is_dir($_SERVER['DOCUMENT_ROOT'] . "/vendor")) {
            return $_SERVER['DOCUMENT_ROOT'] . "/vendor";
        } elseif (!empty($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/../vendor")) {
            return $_SERVER['PWD'] . "/..";
        } elseif (!empty($_SERVER['PWD']) && is_dir($_SERVER['PWD'] . "/vendor")) {
            return $_SERVER['PWD'];
        } elseif (is_dir(getcwd() . "/../vendor")) {
            return getcwd() . "/..";
        } elseif (is_dir(getcwd() . "/vendor")) {
            return getcwd();
        }

        // Fallback to the default document root.
        return $_SERVER['DOCUMENT_ROOT'];
    }
}
