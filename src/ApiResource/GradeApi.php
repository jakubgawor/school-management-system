<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\AddGradeDto;
use App\State\AddGradeStateProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/grade/students/{studentId}',
            input: AddGradeDto::class,
            processor: AddGradeStateProcessor::class,
        ),
    ],
)]
class GradeApi
{

}