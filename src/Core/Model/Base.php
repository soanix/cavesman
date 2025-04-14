<?php

namespace Cavesman\Model;


use Cavesman\Tool\Parser\ClassName;
use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;

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

                $enumName = "\\App\\Enum\\" . ucfirst(ClassName::namespace2Basename(static::class));
                $enumModelName = "\\App\\Enum\\" . ucfirst($modelName);
                $enumModelParentName = "\\App\\Enum\\" . ClassName::namespace2Basename(static::class) . ucfirst($property);
                $enumModelParentNameSingular = "\\App\\Enum\\" . ClassName::namespace2Basename(static::class) . ucfirst($modelName);
                try {
                    $modelReflection = new ReflectionClass($this);
                    $propertyInstance = $modelReflection->getProperty($property);
                    $type = $propertyInstance->getType();

                    if ($type instanceof ReflectionUnionType) {
                        foreach ($type->getTypes() as $unionType) {
                            if ($unionType instanceof ReflectionNamedType && $unionType->getName() === DateTime::class) {
                                $this->{$property} = new DateTime($value);
                                continue 2;
                            }
                        }
                    }elseif ($type instanceof ReflectionNamedType) {
                        if ($type->getName() === Time::class) {
                            $this->{$property} = new Time($value);
                            continue;
                        }elseif ($type->getName() === DateTime::class) {
                            $this->{$property} = new DateTime($value);
                            continue;
                        }
                    }
                } catch (ReflectionException|DateMalformedStringException $e) {

                }

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

    public function json(): static
    {
        $modelReflection = new ReflectionClass($this);

        foreach ($modelReflection->getProperties() as $modelProp) {
            $propName = $modelProp->getName();

            if ($modelReflection->hasProperty($propName)) {
                $value = $modelReflection->getProperty($propName)->getValue($this);

                if ($value instanceof Time) {
                    $this->{$propName} = $value->toString();
                }elseif ($value instanceof \DateTime) {
                    $this->{$propName} = $value->format('Y-m-d\TH:i:s');
                }
            }
        }

        return $this;
    }

}
