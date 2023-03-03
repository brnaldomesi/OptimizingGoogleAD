<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Rules\MaximumLength;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaximumLengthRuleTest extends TestCase
{
    protected $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new MaximumLength();
    }

    /** @test **/
    public function without_modifiers()
    {
        $data = [
            'headline_1'	=>	Str::random(30),
            'headline_2'	=>	Str::random(30),
            'description'	=>	Str::random(80),
            'path_1'		=>	Str::random(15),
            'path_2'		=>	Str::random(15),
        ];

        foreach ($data as $attribute => $value) {
            $this->assertTrue($this->rule->passes($attribute, $value));
        }

        $data = [
            'headline_1'	=>	Str::random(31),
            'headline_2'	=>	Str::random(31),
            'description'	=>	Str::random(81),
            'path_1'		=>	Str::random(16),
            'path_2'		=>	Str::random(16),
        ];

        foreach ($data as $attribute => $value) {
            $this->assertFalse($this->rule->passes($attribute, $value));
        }
    }

    /** @test */
    public function with_modifiers()
    {
        $data = [
            'headline_1'	=>	Str::random(30).'{'.Str::random(20).'}',

            'headline_2'	=>	Str::random(30).'{'.Str::random(20).'}',

            'description'	=>	Str::random(80).'{'.Str::random(20).'}',

            'path_1'		=>	Str::random(15).'{'.Str::random(20).'}',

            'path_2'		=>	Str::random(15).'{'.Str::random(20).'}',
        ];

        foreach ($data as $attribute => $value) {
            $this->assertTrue($this->rule->passes($attribute, $value));
        }

        $data = [
            'headline_1'	=>	Str::random(31).'{'.Str::random(20).'}',

            'headline_2'	=>	Str::random(31).'{'.Str::random(20).'}',

            'description'	=>	Str::random(81).'{'.Str::random(20).'}',

            'path_1'		=>	Str::random(16).'{'.Str::random(20).'}',

            'path_2'		=>	Str::random(16).'{'.Str::random(20).'}',
        ];

        foreach ($data as $attribute => $value) {
            $this->assertFalse($this->rule->passes($attribute, $value));
        }
    }

    /** @test */
    public function with_modifiers_and_defaults()
    {
        $data = [
            'headline_1'	=>	Str::random(20).'{keyword:tools}',

            'headline_2'	=>	Str::random(20).'{keyword:tools}',

            'description'	=>	Str::random(70).'{keyword:tools}',

            'path_1'		=>	Str::random(5).'{keyword:tools}',

            'path_2'		=>	Str::random(5).'{keyword:tools}',
        ];

        foreach ($data as $attribute => $value) {
            $this->assertTrue($this->rule->passes($attribute, $value));
        }

        $data = [
            'headline_1'	=>	Str::random(26).'{keyword:tools}',

            'headline_2'	=>	Str::random(26).'{keyword:tools}',

            'description'	=>	Str::random(76).'{keyword:tools}',

            'path_1'		=>	Str::random(11).'{keyword:tools}',

            'path_2'		=>	Str::random(11).'{keyword:tools}',
        ];

        foreach ($data as $attribute => $value) {
            $this->assertFalse($this->rule->passes($attribute, $value));
        }
    }
}
