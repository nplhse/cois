<?php

namespace App\Message;

use App\Entity\Hospital;
use App\Entity\User;

final class NewUserMessage
{
    private User $user;

    private Hospital $hospital;

    public function __construct(User $user, Hospital $hospital)
    {
        $this->user = $user;
        $this->hospital = $hospital;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHospital(): Hospital
    {
        return $this->hospital;
    }
}
