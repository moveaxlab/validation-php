<?php

namespace ElevenLab\Validation\Rules;

use ElevenLab\Validation\Support\Base64FileUtils;

class MinSizeRule implements Rule
{

    public static function apply(\Illuminate\Validation\Factory $validation)
    {

        $validation->extend('min_size', function($attribute, $value, $parameters, $validator) {

            return Base64FileUtils::size($value) >= $parameters[0];

        }, "The :attribute must be a file with one of the following formats [:params]");

    }

}