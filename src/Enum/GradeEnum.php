<?php

namespace App\Enum;


use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

enum GradeEnum: string
{
    case A = '6.00';
    case A_MINUS = '5.75';
    case B_PLUS = '5.50';
    case B = '5.00';
    case B_MINUS = '4.75';
    case C_PLUS = '4.50';
    case C = '4.00';
    case C_MINUS = '3.75';
    case D_PLUS = '3.50';
    case D = '3.00';
    case D_MINUS = '2.75';
    case E_PLUS = '2.50';
    case E = '2.00';
    case E_MINUS = '1.75';
    case F_PLUS = '1.50';
    case F = '1.00';

    public static function fromString(string $grade): self
    {
        foreach(self::cases() as $case) {
            if ($case->name === $grade) {
                return $case;
            }
        }

        throw new UnprocessableEntityHttpException('Invalid grade.');
    }

}
