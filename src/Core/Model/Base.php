<?php

namespace Cavesman\Model;


use DateMalformedStringException;
use DateTime;
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

                try {
                    $modelReflection = new ReflectionClass($this);
                    $propertyInstance = $modelReflection->getProperty($property);
                    $type = $propertyInstance->getType();

                    if ($type instanceof ReflectionUnionType) {
                        foreach ($type->getTypes() as $unionType) {
                            if ($unionType instanceof ReflectionNamedType && $unionType->getName() === DateTime::class) {
                                if ($value)
                                    $this->{$property} = new DateTime($value);
                                continue 2;
                            }
                        }
                    } elseif ($type instanceof ReflectionNamedType) {
                        if ($type->getName() === Time::class) {
                            if ($value)
                                $this->{$property} = new Time($value);
                            continue;
                        } elseif ($type->getName() === DateTime::class) {
                            if ($value)
                                $this->{$property} = new DateTime($value);
                            continue;
                        }
                    }
                } catch (ReflectionException|DateMalformedStringException $e) {

                }

                $this->{$property} = $this->resolveValue($value, $property);
            }
        }
    }

    private function resolveValue(mixed $value, string $property)
    {

        $className = static::typeOfEnum($property);

        if (!$className)
            $className = static::typeOfCollection($property);

        if (is_array($value)) {
            if (empty($value)) {
                return [];
            }
            // Comprobar si es un array de objetos
            if (array_is_list($value)) {
                return array_map(fn($item) => $this->resolveValue($item, $property), $value);
            } else {
                if (class_exists($className)) {
                    return new $className($value);
                }
                return $value;
            }
        }

        if ($value instanceof \BackedEnum)
            return $value;

        if ($className && enum_exists($className) && $value) {
            return $className::tryFrom($value);
        }


        if ($className && class_exists($className) && $value) {
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

    /**
     * @throws DateMalformedStringException
     */
    public function json(): static
    {
        $modelReflection = new ReflectionClass($this);

        foreach ($modelReflection->getProperties() as $modelProp) {
            $propName = $modelProp->getName();

            if ($modelReflection->hasProperty($propName)) {
                $property = $modelReflection->getProperty($propName);
                $value = $property->getValue($this);
                $type = $property->getType();

                if ($type instanceof ReflectionUnionType) {
                    foreach ($type->getTypes() as $unionType) {
                        if ($unionType instanceof ReflectionNamedType && $unionType->getName() === Time::class) {
                            if ($value) {
                                $time = new Time($value->format('H:i:s'));
                                $this->{$propName} = $time->toString();
                            }
                        } elseif ($unionType instanceof ReflectionNamedType && $unionType->getName() === DateTime::class) {
                            if ($value)
                                $this->{$propName} = $value->format('Y-m-d\TH:i:s');
                        }
                    }
                } elseif ($value instanceof Time) {
                    $this->{$propName} = $value->toString();
                } elseif ($value instanceof \DateTime) {
                    $this->{$propName} = $value->format('Y-m-d\TH:i:s');
                } elseif ($value instanceof Base) {
                    $this->{$propName} = $value->json();
                } elseif (is_array($value) && $value && reset($value) instanceof Base) {
                    array_map(fn(Base $o) => $o->json(), $value);
                } else {
                    if (!$property->isStatic())
                        $this->{$propName} = $value;
                    else
                        get_class($this)::${$propName} = $value;

                }
            }
        }

        return $this;
    }

    public function typeOfEnum($name): \BackedEnum|string|null
    {
        return null;
    }

    public function typeOfCollection(string $property): ?string
    {
        return null;
    }


}
