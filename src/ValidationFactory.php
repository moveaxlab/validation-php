<?php

namespace ElevenLab\Validation;


use ElevenLab\Validation\Rules\PhoneRule;
use ElevenLab\Validation\Rules\Base58Rule;
use ElevenLab\Validation\Rules\Base64EncodedFileRule;
use ElevenLab\Validation\Rules\Base64Rule;
use ElevenLab\Validation\Rules\EqualsRule;
use ElevenLab\Validation\Rules\FileFormatRule;
use ElevenLab\Validation\Rules\FileTypeRule;
use ElevenLab\Validation\Rules\HexRule;
use ElevenLab\Validation\Rules\Iso8601Rule;
use ElevenLab\Validation\Rules\MaxSizeRule;
use ElevenLab\Validation\Rules\MinSizeRule;
use ElevenLab\Validation\Rules\MustBeTrueRule;
use ElevenLab\Validation\Rules\SequenceRule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class ValidationFactory
{

    const LANG_DIR = __DIR__. '/../lang/';
    const DEFAULT_LOCALE = 'en';
    const FILENAME = 'validation.php';

    static $extends = [
        Base64EncodedFileRule::class,
        Iso8601Rule::class,
        Base64Rule::class,
        Base58Rule::class,
        SequenceRule::class,
        EqualsRule::class,
        HexRule::class,
        FileFormatRule::class,
        FileTypeRule::class,
        MinSizeRule::class,
        MaxSizeRule::class,
        MustBeTrueRule::class,
        PhoneRule::class
    ];

    /**
     * Returns an instance of a validator
     *
     * @param mixed $data
     * @param array $rules
     *
     * @return RuleValidator
     */
    public static function make($data, array $rules)
    {

        $files = new Filesystem();
        $loader = new FileLoader($files, self::getLangPath());
        $translator = new Translator($loader, self::DEFAULT_LOCALE);
        $factory = new CustomIlluminateValidationFactory($translator);

        foreach (self::$extends as $extendRule) {
            $extendRule::apply($factory);
        }

        $data = [
            'data' => $data
        ];

        $validator = $factory->make($data, $rules);

        return new RuleValidator($validator);

    }

    /**
     * Get the validation language file path
     *
     * @return string
     */
    protected static function getLangPath()
    {
        return self::LANG_DIR . self::DEFAULT_LOCALE . '/' . self::FILENAME;
    }


}