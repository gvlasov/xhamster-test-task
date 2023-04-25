<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Services\Interfaces\UserModificationLog;

class NullUserModificationLog implements UserModificationLog
{

    public function logCreation(User $user)
    {

    }

    public function logUpdate(User $user)
    {

    }

    public function logDeletion(User $user)
    {

    }

}
