<?php

namespace App\Tests\Traits;

use Doctrine\ORM\Tools\SchemaTool;

trait RecreateDatabaseTrait
{
    protected static function bootKernel(array $options = [])
    {
        $kernel = parent::bootKernel($options);
        self::buildSchema();

        return $kernel;
    }

    protected static function buildSchema(): void
    {
        $container = static::$container ?? static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($meta)) {
            $tool = new SchemaTool($em);
            $tool->dropSchema($meta);
            try {
                $tool->createSchema($meta);
            } catch (ToolsException $e) {
                throw new \InvalidArgumentException("Database schema is not buildable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }
    }
}
