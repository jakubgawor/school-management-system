<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\UserVerificationTokenFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(50);
        UserVerificationTokenFactory::createMany(5);
    }
}
