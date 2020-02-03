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
        $repo->addDataSource(new InMemoryDataSourceAdapter('source-1'));
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
        $repo->addDataSource(new InMemoryDataSourceAdapter('source-1', [$tableName => [$row]]));
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
        $repo->removeDataSource('source-1');
        $this->assertTrue(count($repo->findAll()) === 0);
    }

    public function testPersistNew(): void {
        $repo = ShirtOrderRepository::getInstance();
        $entity = new ShirtOrder();
        $repo->addDataSource(new InMemoryDataSourceAdapter('source-1'));
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

    public function testDeleteExisting(): void {
        $repo = ShirtOrderRepository::getInstance();
        // reset
        $repo->removeDataSource('source-1');
        $repo->addDataSource(new InMemoryDataSourceAdapter('source-2'));
        $entity = $repo->create();
        $entity->waistSize = "hola";
        $repo->persist($entity);

        $result = $repo->findByWaistSize("hola");
        $this->assertEquals(1, count($result));
        $repo->delete(current($result)->id);
        
        $result = $repo->findByWaistSize("foo");
        $this->assertEquals(0, count($result));
    }

    /**
     * Insert, Update, and Delete with multiple data sources
     */
    public function testPersistToMultipleDataSources(): void {
        $repo = ShirtOrderRepository::getInstance();
        $tableName = $repo->getModelClass();

        // remove old data sources and create 2 new datasources
        $repo->removeDataSource('source-2');
        $ds3 = new InMemoryDataSourceAdapter('source-3');
        $repo->addDataSource($ds3);
        $ds4 = new InMemoryDataSourceAdapter('source-4');
        $repo->addDataSource($ds4);

        // create a new entity and persist it
        $entity = $repo->create();
        $entity->waistSize = "some value";
        $repo->persist($entity);

        // both rows should be the same
        $this->assertEquals(
            $ds3->find($tableName, ['waistSize' => "some value"]),
            $ds4->find($tableName, ['waistSize' => "some value"])
        );

        $result = $repo->findByWaistSize("some value");
        $entity = current($result);
        $entity->waistSize = "foo";
        $entityId = $repo->persist($entity);

        // update should be present on both data sources
        $this->assertEquals(
            $ds3->find($tableName, ['waistSize' => "foo"]),
            $ds4->find($tableName, ['waistSize' => "foo"])
        );

        // delete should apply to both data sources
        $repo->delete($entityId);

        $this->assertEmpty($ds3->find($tableName, ['waistSize' => "foo"]));
        $this->assertEmpty($ds4->find($tableName, ['waistSize' => "foo"]));
    }

}
