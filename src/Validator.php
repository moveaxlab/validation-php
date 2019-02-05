<?php


namespace ElevenLab\Validation;


use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;

class Validator extends \Illuminate\Validation\Validator
{

    protected $numericRules = ['Numeric', 'Integer', 'Float'];

    public function __construct(Translator $translator, array $data, array $rules, array $messages = [], array $customAttributes = [])
    {

        $this->implicitRules[] = 'NullableIf';

        parent::__construct($translator, $data, $rules, $messages, $customAttributes);

    }

    public function validateBoolean($attribute, $value)
    {
        return is_bool($value);
    }

    public function validateInteger($attribute, $value)
    {
        return is_int($value);
    }

    public function validateFloat($attribute, $value)
    {
        return is_int($value) || is_float($value);
    }

    public function validateObject($attribute, $value)
    {
        return is_object($value) || (is_array($value) && $value === []) || (is_array($value) && Arr::isAssoc($value));
    }

    public function validateUrl($attribute, $value)
    {

        if(!is_string($value)) return false;

        $pattern = '~^
                    # http:// or https:// or ftp:// or ftps://
                    (?:http|ftp)s?://
                    # domain...
                    (?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+
                    (?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)
                    |
                    # ...or ipv4
                    \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}
                    |
                    # ...or ipv6
                    \[?[A-F0-9]*:[A-F0-9:]+\]?)
                    # optional port
                    (?::\d+)?
                    (?:/?|[/?]\S+)
                    $~ix';

        return preg_match($pattern, $value) > 0;

    }

    public function validateNullableIf($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'nullable_if');

        $other = Arr::get($this->data, $parameters[0]);

        if(boolval($other) === true) {
            return true;
        }

        return $this->validateRequired($attribute, $value);

    }

    public function validateUuid4($attribute, $value, $parameters)
    {
        if(!is_string($value)) return false;

        $regex = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/xi';

        return preg_match($regex, $value);
    }

}