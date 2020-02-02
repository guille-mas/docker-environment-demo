<?php

namespace Befeni\Model\Repository;

use Befeni\Utils\SingletonTrait;

require_once(__DIR__ . "/../../utils/SingletonTrait.php");
require_once(__DIR__."/../entity/ShirtOrder.php");
require_once(__DIR__ . "/AbstractRepository.php");

class ShirtOrderRepository extends AbstractRepository {
    // lets make this class only accesible from a single point
    use SingletonTrait;

    public function getModelClass(): string {
        return '\Befeni\Model\Entity\ShirtOrder';
    }
}
