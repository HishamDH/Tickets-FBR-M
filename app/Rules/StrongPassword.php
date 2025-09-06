<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check minimum length
        if (strlen($value) < 8) {
            $fail('كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل.');
            return;
        }

        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل.');
            return;
        }

        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $fail('كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل.');
            return;
        }

        // Check for number
        if (!preg_match('/[0-9]/', $value)) {
            $fail('كلمة المرور يجب أن تحتوي على رقم واحد على الأقل.');
            return;
        }

        // Check for special character
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('كلمة المرور يجب أن تحتوي على رمز خاص واحد على الأقل (!@#$%^&*...).');
            return;
        }

        // Check for maximum length
        if (strlen($value) > 128) {
            $fail('كلمة المرور يجب أن لا تتجاوز 128 حرف.');
            return;
        }

        // Check for common passwords
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123',
            'password123', 'admin', 'administrator', '1234567890',
            'P@ssw0rd', 'Password123', 'admin123'
        ];

        if (in_array(strtolower($value), array_map('strtolower', $commonPasswords))) {
            $fail('كلمة المرور التي اخترتها شائعة جداً. يرجى اختيار كلمة مرور أقوى.');
            return;
        }

        // Check for sequential characters
        if (preg_match('/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $value)) {
            $fail('كلمة المرور لا يجب أن تحتوي على أحرف أو أرقام متتالية.');
            return;
        }

        // Check for repeated characters
        if (preg_match('/(.)\1{2,}/', $value)) {
            $fail('كلمة المرور لا يجب أن تحتوي على نفس الحرف مكرر أكثر من مرتين متتاليتين.');
            return;
        }
    }
}
