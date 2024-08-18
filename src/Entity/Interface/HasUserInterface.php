<?php

namespace App\Entity\Interface;

use App\Entity\User;

interface HasUserInterface
{
    public function getUser(): ?User;
}
