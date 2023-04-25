<?php

namespace App\Services\Implementations;

use App\Services\Interfaces\ProhibitedWordsList;

class TestProhibitedWordsList implements ProhibitedWordsList
{

    const THE_WORD = 'bollocks';

    public function isViolating(string $text): bool
    {
        return strpos($text, self::THE_WORD) !== false;
    }

}
