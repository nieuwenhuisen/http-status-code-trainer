<?php

namespace App\DataFixtures;

use App\Entity\StatusCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

class StatusCodeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (Response::$statusTexts as $code => $title) {
            $statusCode = new StatusCode($code, $title);
            $this->addReference('status_code_'.$code, $statusCode);
            $manager->persist($statusCode);
        }

        $manager->flush();
    }
}
