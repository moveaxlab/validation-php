<?php

namespace ElevenLab\Validation\Rules;

use ElevenLab\Validation\Support\Base64FileUtils;

class FileTypeRule implements Rule
{

    public static function apply(\Illuminate\Validation\Factory $validation)
    {

        $validation->extend('file_type', function($attribute, $value, $parameters, $validator) {

            return in_array(Base64FileUtils::type($value), $parameters);

        }, "The :attribute must be a file with one of the following formats [:params]");

    }
}