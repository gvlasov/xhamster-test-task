<?php

namespace App\Rules;

use App\Services\Interfaces\ProhibitedWordsList;
use Illuminate\Contracts\Validation\Rule;

class ProhibitedWords implements Rule
{
    protected ProhibitedWordsList $prohibitedWords;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(ProhibitedWordsList $prohibitedWords)
    {
        $this->prohibitedWords = $prohibitedWords;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return !$this->prohibitedWords->isViolating($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute contains a prohibited word';
    }
}
