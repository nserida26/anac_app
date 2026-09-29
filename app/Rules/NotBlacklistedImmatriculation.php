<?php

namespace App\Rules;

use App\Models\ImmatriculationBlacklist;
use Illuminate\Contracts\Validation\Rule;

class NotBlacklistedImmatriculation implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (!is_string($value)) {
            return true;
        }

        return !ImmatriculationBlacklist::isBlacklisted($value);
    }

    public function message(): string
    {
        return trans('trans.immatriculation_blacklisted');
    }
}
