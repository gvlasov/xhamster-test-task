<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TrustedDomains implements Rule
{
    protected \App\Services\Interfaces\TrustedDomains $trustedDomains;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(\App\Services\Interfaces\TrustedDomains $trustedDomains)
    {
        $this->trustedDomains = $trustedDomains;
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
        return $this->trustedDomains->isTrusted($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute domain is not trusted';
    }
}
