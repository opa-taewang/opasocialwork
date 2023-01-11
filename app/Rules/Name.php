<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Name implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $name = explode(' ', $value);
        return (isset($name[0]) && isset($name[1]));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Enter your first name and last name';
    }
}
