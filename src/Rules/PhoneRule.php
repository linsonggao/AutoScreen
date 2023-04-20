<?php


namespace Lsg\AutoScreen\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 手机号验证
 */
final class PhoneRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^1[123456789][0-9]{9}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return sprintf(':attribute 不合法');
    }
}
