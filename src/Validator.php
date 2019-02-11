<?php


namespace ElevenLab\Validation;


use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Support\Arr;

class Validator extends \Illuminate\Validation\Validator
{

    protected $numericRules = ['Numeric', 'Integer', 'Float'];

    public function __construct(TranslatorInterface $translator, array $data, array $rules, array $messages = [], array $customAttributes = [])
    {

        parent::__construct($translator, $data, $rules, $messages, $customAttributes);

        $this->dependentRules[] = 'NullableIf';
        $this->implicitRules[] = 'NullableIf';

    }

    /**
     * Determine if the attribute is validatable.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value)
    {

        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
            $this->passesOptionalCheck($attribute) &&
            ($this->isNotNullIfMarkedAsNullable($attribute, $rule, $value)) &&
            $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute);
    }

    /**
     * Determine if the field is present, or the rule implies required.
     *
     * @param  string  $rule
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value)
    {
        if (is_string($value) && trim($value) === '') {
            return $this->isImplicit($rule);
        }
        return $this->validatePresent($attribute, $value) || $this->isImplicit($rule);
    }


    /**
     * Determine if the attribute passes any optional check.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function passesOptionalCheck($attribute)
    {
        if ($this->hasRule($attribute, ['Sometimes'])) {
            $data = Arr::dot($this->initializeAttributeOnData($attribute));
            $data = array_merge($data, $this->extractValuesForWildcards(
                $data, $attribute
            ));
            return array_key_exists($attribute, $data)
                || in_array($attribute, array_keys($this->data));
        }
        return true;
    }

    protected function isNullable($attribute)
    {
        return $this->hasRule($attribute, ['Nullable']);
    }

    /**
     * Determine if the attribute fails the nullable check.
     *
     * @param  string  $attribute
     * @param  string $rule
     * @param  mixed  $value
     * @return bool
     */
    protected function isNotNullIfMarkedAsNullable($attribute, $rule, $value)
    {
        if (! $this->isNullable($attribute) || $this->isImplicit($rule)) {
            return true;
        }
        return ! is_null($value);
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

        $other = $this->getValue($parameters[0]);

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

    public function validateString($attribute, $value)
    {
        return is_string($value);
    }

    public function validateNullable($attribute, $value)
    {
        return true;

    }

}