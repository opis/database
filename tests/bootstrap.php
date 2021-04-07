<?php
namespace Opis\Database\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use Opis\Database\ORM\Internal\EntityQuery;
use Opis\Database\EntityManager;

/**
 * @param EntityManager|null $instance
 * @return EntityManager
 */
function entityManager(EntityManager $instance = null): EntityManager
{
    static $manager;
    if ($instance !== null) {
        $manager = $instance;
    }
    return $manager;
}

function query(string $class): EntityQuery
{
    return \Opis\Database\Test\entityManager()->query($class);
}

function unique_id(): string
{
    return bin2hex(random_bytes(16));
}

