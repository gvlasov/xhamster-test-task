<?php

namespace App\Services\Interfaces;

interface TrustedDomains
{

    public function isTrusted(string $email): bool;

}
