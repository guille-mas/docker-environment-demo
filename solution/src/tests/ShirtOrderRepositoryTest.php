<?php

use Befeni\DBAL\InMemoryDataSourceAdapter;
use PHPUnit\Framework\TestCase;
use Befeni\Model\Repository\ShirtOrderRepository;
use Befeni\Model\Entity\ShirtOrder;

require_once(__DIR__ . "/../model/repository/ShirtOrderRepository.php");
require_once(__DIR__ . "/../model/entity/ShirtOrder.php");
require_once(__DIR__ . "/../dbal/InMemoryDataSourceAdapter.php");

final class ShirtOrderRepositoryTest extends TestCase
{
    public function testCreateEntity(): void {
        $repo = ShirtOrderRepository::getInstance();
        $this->assertTrue($repo->create() instanceof ShirtOrder);
    }

    public function testFindAllOnEmptyDataSourceShouldReturnEmptyArray(): void
    {
        $repo = ShirtOrderRepository::getInstance();
        $repo->addDataSource(new InMemoryDataSourceAdapter());
        $this->assertTrue(empty($repo->findAll()));
    }

    public function testNonEmptyDataSourceShouldReturnNonEmptyArrayOfObjects(): void {
        $repo = ShirtOrderRepository::getInstance();
        $tableName = $repo->getModelClass();
        $entity = new ShirtOrder();
        $entity->id = 1;
        $entity->customerId = 222;
        $entity->fabricId = 333;
        $entity->collarSize = 20;
        $entity->wristSize = 12;
        $entity->chestSize = 75;
        $entity->waistSize = 70;
        $row = $repo->mapModelToRow($entity);
        $repo->addDataSource(new InMemoryDataSourceAdapter([$tableName => [$row]]));
        $results = $repo->findAll();
        $this->assertTrue(count($results) === 1, "result should contain one single element");
        $this->assertEquals($results[0], $row);
    }

    public function testFind(): void
    {
        $repo = ShirtOrderRepository::getInstance();
        $rows = $repo->find(['id' => 1]);
        $this->assertEquals(count($rows), 1, "find([ id => 1]) by existing id should return a single row");
        $this->assertInstanceOf(ShirtOrder::class, $rows[0], "single row should be instance of ShirtOrder");
        $this->assertEquals(1, $rows[0]->id);
    }


    public function testFindAllShouldReturnEmptyArrayAfterRemovingEveryDataSource(): void {
        $this->assertTrue(count(ShirtOrderRepository::getInstance()->findAll()) === 1);

    }

}
