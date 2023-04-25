<?php

namespace App\Services\Implementations;

use App\Services\Interfaces\TrustedDomains;

class TestTrustedDomains implements TrustedDomains
{

    const THE_BAD_DOMAIN = 'nsa.gov';

    public function isTrusted(string $email): bool
    {
        return !str_ends_with($email, self::THE_BAD_DOMAIN);
    }

}
