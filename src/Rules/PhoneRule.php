<?php

namespace ElevenLab\Validation\Rules;


use Propaganistas\LaravelPhone\Validation\Phone;

class PhoneRule implements Rule
{

    const REGEX = '/^\s*\+(\s*\(?\d\)?-?)*\s*$/x';

    public static function apply(\Illuminate\Validation\Factory $validation)
    {

        $validation->extend('phone', function($attribute, $value, $parameters, $validator) {

            if(!is_string($value) || !preg_match(self::REGEX, $value)) return false;
            $value = trim($value);

            $phoneLib = new Phone();
            return $phoneLib->validate($attribute, $value, $parameters, $validator);

        }, "The :attribute field must be a valid phone number.");

    }

}