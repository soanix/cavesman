<?php

namespace Cavesman\Db\Doctrine\Model;


use BackedEnum;

/**
 * Includes only construct and base methods
 */
abstract class Base
{

    public function __construct(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $modelName = ucfirst($property);
                if (str_ends_with($property, 's')) {
                    $modelName = substr($property, 0, -1);
                }

                $className = "\\App\\Model\\Api\\" . $modelName;

                $enumName = "\\App\\Enum\\" . ucfirst(\App\Tool\Str::namespace2Basename(static::class));
                $enumModelName = "\\App\\Enum\\" . ucfirst($modelName);
                $enumModelParentName = "\\App\\Enum\\" . \App\Tool\Str::namespace2Basename(static::class) . ucfirst($property);
                $enumModelParentNameSingular = "\\App\\Enum\\" . \App\Tool\Str::namespace2Basename(static::class) . ucfirst($modelName);

                $this->{$property} = $this->resolveValue($value, $className, [
                    $enumModelName,
                    $enumModelParentName,
                    $enumModelParentNameSingular,
                    $enumName
                ]);
            }
        }
    }

    private function resolveValue(mixed $value, string $className, array $enumNames)
    {
        if (is_array($value)) {
            if (empty($value)) {
                return [];
            }

            // Comprobar si es un array de objetos
            if (array_is_list($value)) {
                return array_map(function ($item) use ($className, $enumNames) {
                    return $this->resolveValue($item, $className, $enumNames);
                }, $value);
            } else {
                // Si es un array asociativo, tratarlo como un solo objeto
                if (class_exists($className)) {
                    return new $className($value);
                }
                return $value;
            }
        }

        // Intentar crear una instancia de enum
        foreach ($enumNames as $enumName) {
            if (enum_exists($enumName)) {
                return $enumName::tryFrom($value);
            }
        }

        if (class_exists($className) && $value) {
            return new $className($value);
        }

        return $value;
    }

    /**
     * Retrieve raw $_POST request and inject as model instance
     *
     * @return static
     */
    public static function fromRequest(): static
    {
        /** @var array $request */
        $request = json_decode(file_get_contents('php://input'), true);

        return new static($request);
    }

}
