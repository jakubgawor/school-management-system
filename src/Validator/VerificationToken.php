<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class VerificationToken extends Constraint
{
    public $tokenDoesNotExists = 'Token does not exists!';
    public $verifiedUser = 'You are verified!';
    public $expiredMessage = 'Token expired!';
}
