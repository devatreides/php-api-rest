<?php

namespace Api\App\Core\ORM;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Doctrine 
{
    public function getEntityManager()
    {
        return EntityManager::create(
            $this->getConnectionCredentials(), 
            ORMSetup::createAttributeMetadataConfiguration([__DIR__.'/../../Entities'])
        );
    }

    public function getQueryBuilder()
    {
        return DriverManager::getConnection($this->getConnectionCredentials())->createQueryBuilder();
    }

    private function getConnectionCredentials(): array
    {
        $config = include(__DIR__ . '/../../../Config/database.php');

        return $config;
    }
}