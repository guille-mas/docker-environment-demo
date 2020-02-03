<?php

namespace Befeni\DBAL;

require_once(__DIR__."/IDataSourceAdapter.interface.php");

/**
 * A simple data source stored in memory
 * Useful for mocking a data source while writing tests
 */
class InMemoryDataSourceAdapter implements IDataSourceAdapter
{
    private $inMemoryDb = [];


    public function __construct(array $mockDb = []) {
        $this->inMemoryDb = $mockDb;
    }

    /**
     * Return a string id to identify the data source
     */
    function getId(): string {
        return self::class;
    }

    /**
     * Connect to the data source
     */
    function connect() {
        // we wont do nothing here cause this data source is defined in memory
    }

    /**
     * Disconnect from the data source
     */
    function disconnect() {
        // we wont do nothing here cause this data source is defined in memory
    }

    /**
     * Given an array of key => val pairs
     * The implementation of this method must return
     * an array of every tuple that has coincident 
     * values for every given key
     */
    function find(string $collection, array $keyValuePairs): array {
        if(!isset($this->inMemoryDb[$collection])) {
            throw new \Exception('table not found');
        }
        if(!count($keyValuePairs)) {
            throw new \Exception('A condition was expected to match against the data set. Empty array provided instead');
        }

        $filter = function ($item) use ($keyValuePairs) {
            $shouldReturn = true;
            foreach ($keyValuePairs as $key => $value) {
                if (!isset($item[$key]) || $item[$key] !== $value) {
                    $shouldReturn = false;
                    break;
                }
            }
            return $shouldReturn;
        };

        return  array_filter($this->inMemoryDb[$collection], $filter);
    }

    /**
     * Given an array, its implementation
     * must update an existing row
     * or create a new one
     */
    function persist(string $collection, array $row): int {
        if (!is_array($this->inMemoryDb[$collection])) {
            // if the "table" does not exist, lets create it
            $this->inMemoryDb[$collection] = [];
        }
        $foundRows = $this->find($collection, $row);
        
        if(count($foundRows) === 1) {
            // return the existing row
            return current($foundRows);
        } elseif (count($foundRows) > 1) {
            // duplicated rows with same id edge case
            throw new \Exception('dude, I was not expecting to find two rows with the same Primary Key!');
        } elseif(count($this->inMemoryDb) === 0) {
            // assign id to first row
            $row['id'] = 1;
        } else {
            $row['id'] = end($this->inMemoryDb)['id'] + 1;
        }
        // store the new row
        $this->inMemoryDb[$collection][] = $row;
        // re index the collection
        $this->inMemoryDb[$collection] = array_values($this->inMemoryDb[$collection]);
        // return the new row´s primary key
        return $row['id'];
    }

    /**
     * Given an id value
     * should delete from the data store
     * the row with same primary key as $id
     */
    function delete(string $collection, int $id): void {
        if(is_array($this->inMemoryDb[$collection])) {
            $idxToDelete = null;
            foreach($this->inMemoryDb[$collection] as $idx => $row) {
                if($row['id'] === $id) {
                    $idxToDelete = $idx;
                    break;
                }
            }
            if($idxToDelete !== null) {
                // delete row
                unset($this->inMemoryDb[$collection][$idx]);
                // re index collection
                $this->inMemoryDb[$collection] = array_values($this->inMemoryDb[$collection]);
            }
        }
    }

    /**
     * Return all rows from a given collection
     */
    function findAll(string $collection): array {
        return $this->inMemoryDb[$collection] ?? [];
    }
}
