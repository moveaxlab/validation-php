<?php

namespace ElevenLab\Validation;


use Illuminate\Validation\ValidationException;

class RuleValidator
{

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Validator $validator)
    {

        $this->validator = $validator;

    }

    /**
     * Validate the class instance.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        if(!$this->validator->passes()) {
            throw new ValidationException($this->validator);
        }
    }

    /**
     * Returns the validator instance
     *
     * @return Validator
     */
    public function validator()
    {
        return $this->validator;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->validator, $name)) {
            return $this->validator->$name(...$arguments);
        }
    }

}