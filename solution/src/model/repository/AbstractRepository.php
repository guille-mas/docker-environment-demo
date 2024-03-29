<?php

namespace Befeni\Model\Repository;

use Befeni\DBAL\IDataSourceAdapter;

require_once(__DIR__."/IDataMapper.interface.php");

/**
 * Base Model Repository
 * Support multiple IDataSourceAdapter
 * We assume each IDataSourceAdapter instance its kind of a "replica".
 * This means every change to the model 
 * must be propagated to every registered source.
 * 
 * We assume also that every Primary Key will be an integer named id
 * 
 * Since we don't have a service container in place, 
 * and because there must be a single entry point per Model Repository
 * this class and every subclass is a Singleton
 */
abstract class AbstractRepository implements IDataMapper
{
    
    /**
     * Stores each instance of IDataSourceAdapter
     * indexed by IDataSourceAdapter::getId()
     */
    protected $dataSources = [];

    protected $modelColumns = [];
    
    /**
     * Return the class that represents the model
     */
    abstract function getModelClass(): string;

    protected function __construct()
    {
        $modelClass = $this->getModelClass();
        $this->modelColumns = array_keys((array) new $modelClass());
    }


    /**
     * Maps a data row to a model class
     */
    function mapRowToModel(array $row): object {
        $modelClass = $this->getModelClass();
        $obj = new $modelClass();
        foreach($row as $col => $value) {
            $obj->{$col} = $value;
        }
        return $obj;
    }

    /**
     * @see https://ocramius.github.io/blog/fast-php-object-to-array-conversion/
     */
    public function mapModelToRow(object $model): array
    {
        return (array) $model;
    }

    /**
     * Allows to inject a new data source
     */
    public function addDataSource(IDataSourceAdapter $source) {
        $this->dataSources[$source->getId()] = $source;
    }

    /**
     * Allow to remove a previously injected data source
     */
    public function removeDataSource(string $dataSourceId) {
        unset($this->dataSources[$dataSourceId]);
    }

    public function delete(int $idValue) {
        foreach($this->dataSources as $k => &$ds) {
            $ds->delete($this->getModelClass(), $idValue);
        }
    }

    /**
     * 
     */
    public function persist(object $model): int{
        // check that given model is supported by the repository
        $instanceClass = get_class($model);
        if($instanceClass != $this->getModelClass()) {
            throw new \Exception("This repository expect instances of model ".$this->getModelClass()."$instanceClass given instead");
        }
        $row = $this->mapModelToRow($model);
        $id = null;
        foreach ($this->dataSources as $k => $ds) {
            $id = $ds->persist($this->getModelClass(), $row);
        }
        return $id;
    }

    /**
     * Factory method that returns a new instance 
     * of this repository supported model class
     */
    public function create() {
        $modelClass = $this->getModelClass();
        return new $modelClass();
    }


    /**
     * 
     */
    public function find(array $colValueArray): array {
        $rows = [];
        foreach ($this->dataSources as $k => $ds) {
            $rows = $ds->find($this->getModelClass(), $colValueArray);
            if(is_array($rows) && !empty($rows)){
                // found it. no need to look up in another data source
                break;
            }
        }
        return array_map(function ($item) {
            return $this->mapRowToModel($item);
        }, $rows);
    }

    /**
     * 
     */
    public function findAll(): array {
        if(count($this->dataSources) === 0) {
            return [];
        } else {
            $allRows = current($this->dataSources)->findAll($this->getModelClass());
            return array_map(function ($item) {
                return $this->mapRowToModel($item);
            }, $allRows);
        }
    }

    /**
     * Used to provide a findBy*($value)
     * @todo: implement
     */
    public function __call($name, $arguments): array
    {
        if(strpos($name, 'findBy') === 0) {
            // findBy($column, $value) magic method
            $column = lcfirst(ltrim($name, 'findBy'));
            $modelClass = $this->getModelClass();
            if(isset($arguments[0]) && in_array($column, $this->modelColumns)) {
                $valueToFind = $arguments[0] ?? null;
                // if name exists as an attribute of model class
                if(empty($this->dataSources)) {
                    // but no data source is found, return an empty array
                    return [];
                } else {
                    // return the result from find([$column => $value])
                    return $this->find([$column => $valueToFind]);
                }
            }
        }
        // else we don't support the method
        throw new \Exception("Unsupported method ". $name);
    }

}
