<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Dto\AddGradeDto;
use App\State\Grade\AddGradeStateProcessor;
use App\State\Grade\DeleteGradeStateProcessor;
use App\State\Grade\GradesAverageStateProvider;
use App\State\Grade\GradeStateProvider;

#[ApiResource(
    shortName: 'Grade',
    operations: [
        new Post(
            uriTemplate: '/grade/students/{studentId}',
            security: 'is_granted("ROLE_TEACHER")',
            input: AddGradeDto::class,
            processor: AddGradeStateProcessor::class
        ),
        new Get(
            uriTemplate: '/grades/students/{studentId}/{subjectName}',
            security: 'is_granted("ROLE_STUDENT")',
            provider: GradeStateProvider::class
        ),
        new Get(
            uriTemplate: '/grades/student/{studentId}/{subjectName}/average',
            security: 'is_granted("VIEW_AVERAGE", object)',
            provider: GradesAverageStateProvider::class,
        ),
        new Delete(
            uriTemplate: '/grade/{gradeId}',
            security: 'is_granted("ROLE_TEACHER")',
            provider: GradeStateProvider::class,
            processor: DeleteGradeStateProcessor::class
        )
    ],
)]
class GradeApi
{

}