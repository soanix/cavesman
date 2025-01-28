<?php

namespace Cavesman\Tool\Parser;

class ClassName
{

    /**
     * Get array of declared classes in namespace
     * @param $namespace
     * @return array
     */
    public static function listInNamespace($namespace)
    {
        $namespace .= '\\';
        $myClasses = array_filter(get_declared_classes(), fn($item) => str_starts_with($item, $namespace));
        $theClasses = [];
        foreach ($myClasses as $class) {
            $theParts = explode('\\', $class);
            $theClasses[] = $class;
        }
        return $theClasses;
    }

    /**
     * Convert Namespace to basename
     *
     * @param string $classname
     * @return string
     */
    public static function namespace2Basename(string $classname): string
    {
        return substr(strrchr($classname, "\\"), 1);
    }

    /**
     * Convert CamleCase to snake_case
     *
     * @param string $classname
     * @return string
     */
    public static function camle2Snake(string $camleCase): string
    {

        $result = '';

        for ($i = 0; $i < strlen($camleCase); $i++) {
            $char = $camleCase[$i];

            if (ctype_upper($char)) {
                $result .= '_' . strtolower($char);
            } else {
                $result .= $char;
            }
        }

        return ltrim($result, '_');

    }
}
