<?php

use Cavesman\Console;
use Cavesman\FileSystem;
use Cavesman\Tool\Parser\ClassName;

Console::show('Entity Value', Console::INFO);
$entityName = ucfirst(Console::requestValue('Entity name in CammleCase (Upper):'));

$extends = strtolower(Console::requestValue('Class Extends (optional)'));
$file = FileSystem::srcDir() . '/Entity/' . $entityName . '.php';

$fields = [];

do {
    $addField = strtolower(Console::requestValue('¿Deseas agregar un campo? (y/n)')) === 'y';

    if ($addField) {
        $fieldName = Console::requestValue('Nombre del campo (en camelCase)');
        $fieldType = strtolower(Console::requestValue('Tipo de dato (string, datetime, date, boolean, time, float, integer, text)'));
        $nullable = strtolower(Console::requestValue('¿Es nullable? (y/n)')) === 'y';
        $unique = strtolower(Console::requestValue('¿Es único? (y/n)')) === 'y';
        $default = strtolower(Console::requestValue('¿Valor por defecto? (y/n)')) === 'y';

        $defaultValue = null;

        if ($default)
            $defaultValue = Console::requestValue('Indica el valor');


        $fields[] = [
            'name' => $fieldName,
            'type' => $fieldType,
            'nullable' => $nullable,
            'unique' => $unique,
            'default' => $defaultValue,
        ];
    }
} while ($addField);

// Generar el archivo PHP de la entidad
$code = '<?php' . PHP_EOL . PHP_EOL .
    'namespace App\Entity;' . PHP_EOL . PHP_EOL .
    'use Doctrine\ORM\Mapping\Column;' . PHP_EOL .
    'use Doctrine\ORM\Mapping\Entity;' . PHP_EOL . PHP_EOL .
    '#[Entity]' . PHP_EOL .
    'class ' . $entityName . ($extends ? ' extends ' . $extends : '') . PHP_EOL .
    '{' . PHP_EOL;

foreach ($fields as $field) {
    $code .= PHP_EOL;
    $code .= '    #[Column(name: "' . ClassName::camle2Snake($field['name']) . '", type: "' . $field['type'] . '"';

    if ($field['nullable']) {
        $code .= ', nullable: true';
    }

    if ($field['unique']) {
        $code .= ', unique: true';
    }

    if ($field['default'] !== null) {
        $code .= ', options: ["default" => ' . (is_numeric($field['default']) || is_bool($field['default']) ? $field['default'] : ('"' . $field['default'] . '"')) . ']';
    }

    $code .= ')]' . PHP_EOL;
    $code .= '    private $' . $field['name'] . ';' . PHP_EOL;
}

// Cerrar la clase
$code .= PHP_EOL . '}';

// Escribir el código en el archivo
$fp = fopen($file, "w+");
fwrite($fp, $code);
fclose($fp);
exit();
