<?php

namespace ElevenLab\Validation\Tests;


use Illuminate\Validation\ValidationException;
use ElevenLab\Validation\RuleMapper;
use ElevenLab\Validation\Spec;
use ElevenLab\Validation\ValidationFactory;

class ValidationTest extends TestCase
{

    public function typesDataProvider()
    {

        $data = file_get_contents(__DIR__.'/vectors.json');
        $vectors = json_decode($data);
        $types = $vectors->types;
        $provider = [];


        foreach ($types as $type => $outcomes) {

            foreach ($outcomes->success as $value) {
                $provider[] = [
                    $type,
                    $value,
                    true
                ];
            }

            foreach ($outcomes->failure as $value) {

                $provider[] = [
                    $type,
                    $value,
                    false
                ];

            }

        }

        return $provider;

    }

    public function specsDataProvider()
    {

        $data = file_get_contents(__DIR__.'/vectors.json');
        $vectors = json_decode($data, true);
        $specs = $vectors["specs"];

        $provider = [];

        foreach ($specs as $spec)
        {

            foreach ($spec["success"] as $value) {
                $provider[] = [
                    $spec['spec'],
                    $value,
                    true,
                    null
                ];
            }

            foreach ($spec["failure"] as $failureData) {

                $provider[] = [
                    $spec['spec'],
                    $failureData['data'],
                    false,
                    $failureData['failing']
                ];

            }

        }

        return $provider;

    }

    /**
     * @dataProvider typesDataProvider
     */
    public function testTypesValidation($type, $value, $success)
    {

        $ruleMapper = new RuleMapper();
        $rule = $ruleMapper->mapType($type);

        $rules = [
            "data" => $rule
        ];

        $validator = ValidationFactory::make($value, $rules);

        $passes = $validator->passes();

        $this->assertEquals($success, $passes);

    }

    /**
     * @dataProvider specsDataProvider
     */
    public function testSpecValidation($spec, $data, $success, $failingInfo)
    {

        $rules = Spec::parse($spec)->toValidationArray();

        $validator = ValidationFactory::make($data, $rules);

        if(!$success) {
            $this->expectException(ValidationException::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        try {
            $validator->validate();
        } catch (ValidationException $ex) {
            throw $ex;
        }

    }


}