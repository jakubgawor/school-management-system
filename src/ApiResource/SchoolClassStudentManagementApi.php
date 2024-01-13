<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\SchoolClassStudentManagementStateProcessor;
use App\State\SchoolClassStudentManagementStateProvider;

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
    provider: SchoolClassStudentManagementStateProvider::class,
    processor: SchoolClassStudentManagementStateProcessor::class,
)]
class SchoolClassStudentManagementApi
{
    public ?StudentApi $student = null;

    /** School class name */
    public ?string $name = null;
}