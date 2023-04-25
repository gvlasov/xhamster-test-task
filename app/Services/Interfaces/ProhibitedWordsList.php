<?php

namespace App\Services\Interfaces;

interface ProhibitedWordsList
{

    public function isViolating(string $text): bool;

}
