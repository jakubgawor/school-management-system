<?php

namespace App\Tests\Unit\Validator;

use App\Entity\SchoolClass;
use App\Repository\SchoolClassRepository;
use App\Validator\UniqueSchoolClassName;
use App\Validator\UniqueSchoolClassNameValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueSchoolClassNameValidatorTest extends TestCase
{
    private SchoolClassRepository $schoolClassRepository;
    private UniqueSchoolClassNameValidator $validator;
    private ExecutionContextInterface $context;

    public function setUp(): void
    {
        $this->schoolClassRepository = $this->createMock(SchoolClassRepository::class);

        $this->validator = new UniqueSchoolClassNameValidator($this->schoolClassRepository);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    /** @test */
    public function validate_with_not_unique_school_class_name()
    {
        $constraint = new UniqueSchoolClassName();

        $this->schoolClassRepository->method('findOneBy')->willReturn(new SchoolClass());

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

        $this->validator->validate('1a', $constraint);
    }

    /** @test */
    public function validate_with_unique_email()
    {
        $constraint = new UniqueSchoolClassName();

        $this->schoolClassRepository->method('findOneBy')->willReturn(null);

        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('1a', $constraint);
    }
}