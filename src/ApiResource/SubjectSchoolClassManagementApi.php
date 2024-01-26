<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Dto\SubjectSchoolClassDto;
use App\State\Subject\SubjectSchoolClassManagementStateProcessor;
use App\State\Subject\SubjectSchoolClassManagementStateProvider;

#[ApiResource(
    shortName: 'Subject management',
    operations: [
        new Post(
            uriTemplate: '/subjects/classes/add',
            openapi: new Operation(
                summary: 'Add a school class to the subject',
                description: 'Add a school class to the subject',
            ),
        ),
        new Delete(
            uriTemplate: '/subjects/{subjectId}/classes/{schoolClassName}',
            openapi: new Operation(
                summary: 'Remove a school class from a subject',
                description: 'Remove a school class from a subject',
            ),
            provider: SubjectSchoolClassManagementStateProvider::class,
        ),
    ],
    input: SubjectSchoolClassDto::class,
    security: 'is_granted("ROLE_ADMIN")',
    processor: SubjectSchoolClassManagementStateProcessor::class
)]
class SubjectSchoolClassManagementApi
{
}









































































