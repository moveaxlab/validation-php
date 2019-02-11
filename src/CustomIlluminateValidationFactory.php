<?php

namespace ElevenLab\Validation;


use Illuminate\Validation\Factory;

class CustomIlluminateValidationFactory extends Factory
{

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
         return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = parent::make($data, $rules, $messages, $customAttributes);
        return $validator;
    }

}