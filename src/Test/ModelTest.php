<?php
declare(strict_types=1);

namespace Cavesman\Test;


use Cavesman\Test\Entity\Entity;
use Cavesman\Test\Model\Model;
use Exception;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    /**
     * @group Twig
     * @return void
     * @throws Exception
     */
    public function testModel2Entity(): void
    {
        $model = new Model(['name' => 'Pedro', 'models' => [new Model(['name' => 'Jordi'])]]);
        $this->assertEquals('Pedro', $model->name);

        $entity = $model->entity(Entity::class);

        $this->assertInstanceOf(Entity::class, $entity);

        $this->assertEquals($model->name, $entity->name);

        foreach ($model->children as $child) {
            $this->assertEquals($child->name, $child->entity(Entity::class)->name);
        }

    }
}
