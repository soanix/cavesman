<?php

namespace Cavesman\Tool\Parser;

use Cavesman\Enum\Directory;
use Cavesman\FileSystem;

class ClassName
{

    /**
     * Get array of declared classes in namespace
     * @param $namespace
     * @return array
     */
    public static function listInNamespace($namespace): array
    {

        if(str_starts_with($namespace, '\\'))
            $namespace = substr($namespace, 1);
        // Convertir el namespace en una ruta de directorio
        $namespacePath = str_replace('\\', '/', $namespace);

        // Obtener la ruta base del namespace
        $basePath = FileSystem::getPath(Directory::SRC); // Ajusta __DIR__ según tu estructura de directorios

        // Inicializar un array para almacenar las clases
        $classes = [];

        // Crear un iterador recursivo para explorar el directorio
        $directoryIterator = new \RecursiveDirectoryIterator($basePath);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        // Recorrer todos los archivos PHP en el directorio
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                // Obtener el contenido del archivo
                $fileContent = file_get_contents($file->getPathname());

                // Buscar el namespace declarado en el archivo
                if (preg_match('/namespace\s+([^\s;]+)/', $fileContent, $matches)) {
                    $fileNamespace = $matches[1];

                    // Verificar si el namespace del archivo coincide con el buscado
                    if ($fileNamespace === $namespace) {
                        // Obtener el nombre de la clase
                        if (preg_match('/class\s+([^\s{]+)/', $fileContent, $classMatches)) {
                            $className = $fileNamespace . '\\' . $classMatches[1];
                            $classes[] = '\\' . $className;

                            // Is posible that class not included (psr-4 behavior)
                            if(!class_exists('\\' . $className))
                                require_once $file->getPathname();
                        }
                    }
                }
            }
        }

        return $classes;
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
     * @param string $camleCase
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
