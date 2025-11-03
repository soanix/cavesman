<?php

namespace Cavesman\Enum;

/**
 * Enum FileSystem
 *
 * Represents predefined paths within the application's file system.
 */
enum Directory: string
{
    case SRC = '/src';             // Path to the source directory
    case PUBLIC = '/public';       // Path to the public directory
    case APP = '/app';             // Path to the application root
    case CONFIG = '/app/config';   // Path to the configuration directory
    case LOG = '/app/log';          // Path to the locale directory
    case LOCALE = '/app/locale';   // Path to the configuration directory
    case CONTROLLER = '/src/Controller'; // Path to the controllers directory
    case ROUTES = '/src/Routes';   // Path to the routes directory
    case ENTITY = '/src/Entity';   // Path to the entities directory
    case MODELS = '/src/Model';   // Path to the models directory
    case ENUM = '/src/Enum';       // Path to the enums directory
    case COMMANDS = '/src/Commands'; // Path to the commands directory
    case EXCEPTION = '/src/Exception'; // Path to the exceptions directory
    case INSTALL = '/src/Install'; // Path to the installation scripts directory
    case MODULE = '/src/Module';   // Path to the modules directory
    case TEST = '/src/Test';       // Path to the tests directory
    case TOOL = '/src/Tool';       // Path to the tools directory
    case VIEWS = '/src/Views';     // Path to the views directory

    /**
     * Attempts to find an enum case by its name.
     *
     * This method searches through all the enum cases and returns the matching case
     * based on its name (case-insensitive). If no match is found, it returns null.
     *
     * @param string $name The name of the enum case to search for (e.g., "SRC", "CONFIG").
     * @return ?self The matching enum case, or null if no match is found.
     */
    public static function tryFromName(string $name): ?self
    {
        return array_find(
            self::cases(),
            fn($case) => strcasecmp($case->name, $name) === 0
        );
    }
}
