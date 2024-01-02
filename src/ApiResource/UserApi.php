<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;

#[ApiResource(
    shortName: 'User',
    stateOptions: new Options(User::class),
)]
class UserApi
{

}