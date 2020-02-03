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
        $this->assertEquals($results[0], $entity);
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
        $repo = ShirtOrderRepository::getInstance();
        $this->assertTrue(count($repo->findAll()) === 1);
        $repo->removeDataSource((new InMemoryDataSourceAdapter())->getId());
        $this->assertTrue(count($repo->findAll()) === 0);
    }

    public function testPersistNew(): void {
        $repo = ShirtOrderRepository::getInstance();
        $entity = new ShirtOrder();
        $repo->addDataSource(new InMemoryDataSourceAdapter());
        $entity->customerId = 222;
        $entity->fabricId = 333;
        $entity->collarSize = 20;
        $entity->wristSize = 12;
        $entity->chestSize = 75;
        $entity->waistSize = 70;
        $repo->persist($entity);
        $persistedEntities = $repo->findAll();
        $this->assertEquals(1, count($persistedEntities));
        $entity->id = 1;
        $this->assertEquals($entity, $persistedEntities[0]);
    }


    public function testSuccessFindById(): void {
        $repo = ShirtOrderRepository::getInstance();
        $this->assertEquals(1, count($repo->findById(1)));
    }

    public function testSuccessFindByCustomerId(): void {
        $repo = ShirtOrderRepository::getInstance();
        $result = $repo->findByCustomerId(222);
        $this->assertEquals(1, count($result));
        $this->assertEquals(222, $result[0]->customerId);
    }

    public function testSuccessFindByFabricId(): void {
        $repo = ShirtOrderRepository::getInstance();
        $result = $repo->findByFabricId(333);
        $this->assertEquals(1, count($result));
        $this->assertEquals(333, $result[0]->fabricId);
    }

    public function testEmptyFindById(): void {
        $repo = ShirtOrderRepository::getInstance();
        $result = $repo->findById(1);
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]->id);
    }

    public function testEmptyFindByWaistSize(): void {
        $repo = ShirtOrderRepository::getInstance();
        $result = $repo->findByWaistSize(70);
        $this->assertEquals(1, count($result));
        $this->assertEquals(70, $result[0]->waistSize);
    }


    public function testUpdateExisting(): void {
        $repo = ShirtOrderRepository::getInstance();
        $result = $repo->findById(1);
        $entity = $result[0];
        $entity->waistSize = "foo";
        $repo->persist($entity);
        $result = $repo->findByWaistSize("foo");
        $this->assertEquals("foo", current($result)->waistSize);
    }

    // public function testDeleteExisting(): void {}

    // public function testDeleteNonExisting(): void {}

}
