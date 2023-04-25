<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserModificationLog
{

    public function logCreation(User $user);

    public function logUpdate(User $user);

    public function logDeletion(User $user);

}
