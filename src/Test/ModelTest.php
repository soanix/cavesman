<?php
declare(strict_types=1);

namespace Cavesman\Test;


use Cavesman\Test\Entity\Entity;
use Cavesman\Test\Model\Model;
use Cavesman\Test\Model\ModelBase;
use DateTime;
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
        $model = new Model([
            'name' => 'Pedro',
            'date' => '2025-12-01T10:00',
            'children' => [
                ['name' => 'Jordi']
            ]
        ]);

        $this->assertEquals('Pedro', $model->name);

        $this->assertInstanceOf(DateTime::class, $model->date);



    }

    /**
     * @group Twig
     * @return void
     * @throws Exception
     */
    public function testEntity2Model(): void
    {
        $entity = new Entity();
        $entity->name = 'Pedro';
        $entity->date = new DateTime();
        $child = new Entity();
        $child->name = 'Jordi';
        $entity->children->add($child);


        $this->assertEquals('Pedro', $entity->name);

        $model = $entity->model(Model::class);

        $this->assertInstanceOf(Model::class, $model);

        $this->assertEquals($entity->name, $entity->name);

        foreach ($entity->children as $child) {
            $this->assertEquals($child->name, $child->model(Model::class)->name);
        }

        $json = $model->json();
        $this->assertIsString($json->date);


    }
}
