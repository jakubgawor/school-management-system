<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\EmailDto;
use App\Dto\ResetPasswordDto;
use App\Service\ResetPasswordService;
use App\Service\TokenNotificationService;
use App\Service\TokenService;
use App\Service\UserService;

class ResetPasswordStateProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenService             $tokenService,
        private ResetPasswordService     $resetPasswordService,
        private TokenNotificationService $tokenNotificationService,
        private UserService              $userService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof EmailDto) {
            $user = $this->userService->findUserByEmail($data->email);

            $token = $this->tokenService->createToken($user->getId());

            $this->tokenNotificationService->sendTokenNotification($token);

        }

        if ($data instanceof ResetPasswordDto) {
            $this->resetPasswordService->resetPassword(
                $data->token,
                $data->password
            );

        }

        return $data;
    }
}
