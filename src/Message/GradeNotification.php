<?php

namespace App\Message;

class GradeNotification
{
    public function __construct(
        private string $sendTo,
        private string $gradeValue,
    )
    {
    }

    public function getSendTo(): string
    {
        return $this->sendTo;
    }

    public function getGradeValue(): string
    {
        return $this->gradeValue;
    }
}