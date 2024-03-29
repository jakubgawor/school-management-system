<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\SchoolClass\SchoolClassStudentManagementStateProcessor;
use App\State\SchoolClass\SchoolClassStudentManagementStateProvider;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    uriTemplate: '/class/add',
    shortName: 'Class management',
    operations: [
        new Post(
            uriTemplate: '/classes/students/add',
            openapi: new Operation(
                summary: 'Add student to a class',
                description: 'Add student to a class'
            )
        ),
        new Delete(
            uriTemplate: '/classes/{schoolClassName}/students/{studentId}',
            openapi: new Operation(
                summary: 'Remove the student from the class',
                description: 'Remove the student from the class'
            )
        )
    ],
    security: 'is_granted("ROLE_TEACHER")',
    provider: SchoolClassStudentManagementStateProvider::class,
    processor: SchoolClassStudentManagementStateProcessor::class,
)]
class SchoolClassStudentManagementApi
{
    #[NotBlank]
    public ?StudentApi $student = null;

    /** School class name */
    #[NotBlank]
    public ?string $name = null;
}