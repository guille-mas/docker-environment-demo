<?php

namespace Befeni\Model\Repository;

use Befeni\Model\Entity\ShirtOrder;

require_once(__DIR__."/../entity/ShirtOrder.php");
require_once(__DIR__ . "/AbstractRepository.php");

class ShirtOrderRepository extends AbstractRepository {
    
    public function getModelClass(): string {
        return '\Befeni\Model\Entity\ShirtOrder';
    }
}
