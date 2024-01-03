<?php

namespace App\Message;

class VerifyMailNotification
{
    public function __construct(
        private string $content
    )
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}