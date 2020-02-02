<?php

namespace Befeni\Model\Repository;

use Befeni\DBAL\IDataSourceAdapter;

require_once(__DIR__."/IDataMapper.interface.php");

/**
 * Base Model Repository
 * Support multiple IDataSourceAdapter
 * We assume each IDataSourceAdapter instance is a replica.
 * This means every change to the model 
 * must be propagated to every registered source.
 * 
 * We assume also that every Primary Key will be an integer named id
 */
class AbstractRepository implements IDataMapper
{

    /**
     * Stores each instance of IDataSourceAdapter
     * indexed by IDataSourceAdapter::getId()
     */
    protected $dataSources = [];
    
    /**
     * Return the class that represents the model
     */
    abstract function getModelClass();

    /**
     * Maps a data row to a model class
     */
    abstract protected function mapRowToModel(array $row): object;

    /**
     * @see https://ocramius.github.io/blog/fast-php-object-to-array-conversion/
     */
    protected function mapModelToRow(object $model): array
    {
        $row = (array) $model;
        return $row;
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
        foreach($this->dataSources as $k => $ds) {
            $ds->delete($idValue);
        }
    }

    /**
     * 
     */
    public function persist(object $model) {
        // check that given model is supported by the repository
        $instanceClass = get_class($model);
        if($instanceClass != $this->getModelClass()) {
            throw new \Exception("This repository expect instances of model ".$this->getModelClass()."$instanceClass given instead");
        }
        $row = $this->mapModelToRow($model);
        foreach ($this->dataSources as $k => $ds) {
            $ds->persist($row);
        }
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
    protected function find(array $colValueArray) {
        $row = null;
        foreach ($this->dataSources as $k => $ds) {
            $row = $ds->find($colValueArray);
            if(is_array($row)){
                break;
            }
        }
        return $row;
    }

    /**
     * Used to provide a findBy*($value)
     * @todo: implement
     */
    public function __call($name, $arguments)
    {
        throw new \Exception("calling $name method with arguments: ".implode(' ', $arguments));
    }

}
