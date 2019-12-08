<?php

namespace App\DataFixtures;

use App\Entity\StatusCode;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

class StatusCodeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (Response::$statusTexts as $code => $title) {
            $statusCode = new StatusCode($code, $title);
            $manager->persist($statusCode);
        }

        $manager->flush();
    }
}
