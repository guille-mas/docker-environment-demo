<?php

namespace Befeni\DBAL;

/**
 * Provide a common interface to every
 * data source adapter implementation
 */
interface IDataSourceAdapter
{
    /**
     * Return a string id to identify the data source
     */
    function getId(): string;
    
    /**
     * Connect to the data source
     */
    function connect();
    
    /**
     * Disconnect from the data source
     */
    function disconnect();

    /**
     * Given an array of key => val pairs
     * The implementation of this method must return
     * an array of every tuple that has coincident 
     * values for every given key
     */
    function find(Array $keyValuePairs): array;

    /**
     * Given an array, its implementation
     * must update an existing row
     * or create a new one
     */
    function persist(Array $row): void;

    /**
     * Given an id value
     * should delete from the data store
     * the row with same primary key as $id
     */
    function delete(int $id): void;

    /**
     * Return all rows 
     */
    function findAll(): array;
}
