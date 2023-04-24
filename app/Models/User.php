<?php

namespace App\Models;

use app\Services\Interfaces\UserModificationLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\App;
use MinuteOfLaravel\Validation\SelfValidatingModel;

class User extends SelfValidatingModel
{
    use HasFactory;


}
