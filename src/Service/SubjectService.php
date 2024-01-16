<?php

namespace App\Service;

use App\Entity\Teacher;
use App\Repository\SubjectRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SubjectService
{
    public function __construct(
        private SubjectRepository $subjectRepository,
    )
    {
    }

    public function validateSameNameSubject(string $name, Teacher $teacher): void
    {
        $subjects = $this->subjectRepository->findByName($name);

        if($subjects) {
            foreach ($subjects as $subject) {
                if($subject->getTeacher()->getId() === $teacher->getId()) {
                    throw new UnprocessableEntityHttpException('This teacher already teaches a subject by that name!');
                }
            }
        }

    }


}