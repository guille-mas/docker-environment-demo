<?php

namespace Befeni\Model\Repository;

/**
 * Provides a common interface to map a row to a model class
 */
interface IDataMapper {
    private function mapRowToModel(Array $row): object;
}
