<?php

use SmartCms\Core\Services\Schema\SchemaParser;

function testParse(string $type, mixed $value, mixed $toBe = null, ?string $test_name = null)
{
    $test_name = $test_name ?? "can parse $type variable";
    it($test_name, function () use ($type, $value, $toBe) {
        $toBe = $toBe ?? $value;
        $values = ['test' => $value];
        $parsedValues = SchemaParser::make([schema($type)], $values);
        expect($parsedValues)->toBeArray();
        expect($parsedValues)->toHaveCount(1);
        expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBe($toBe);
    });
}

testParse('text', 'test');
testParse('text', null, '', 'can parse text variable with null value');
testParse('number', 1);
testParse('number', '', 0, 'can parse number variable with empty value');
testParse('bool', true);
testParse('image', '/test.jpg', 'http://localhost/storage/test.jpg');
testParse('image', null, no_image(), 'can parse image variable with null value');
testParse('file', '/test.pdf', 'http://localhost/storage/test.pdf');
// testParse('file', null, '', 'can parse file variable with null value'); @todo refactor file for default variable
testParse('heading', [
    'heading_type' => 'h1',
    'use_page_heading' => true,
    'use_page_name' => false,
    'use_custom' => false,
]);
testParse('heading', '', [
    'heading_type' => 'h1',
    'use_page_heading' => true,
    'use_page_name' => false,
    'use_custom' => false,
], 'can parse heading variable with empty value');
testParse('description', [
    'heading_type' => 'h1',
    'is_description' => true,
    'is_summary' => false,
    'is_custom' => false,
]);

testParse('socials', [1], []);
testParse('socials', 1, [], 'can parse social variable with non array value');
testParse('phones', [1], []);
testParse('phones', 1, [], 'can parse phones variable with non array value');
testParse('phone', 1, '');
testParse('phone', null, '', 'can parse phone variable with null value');
testParse('emails', [1], []);
testParse('emails', 1, [], 'can parse emails variable with non array value');
testParse('email', 1, '');
testParse('email', null, '', 'can parse email variable with null value');
testParse('addresses', [1], []);
testParse('addresses', 1, [], 'can parse addresses variable with non array value');
testParse('address', 1, '');
testParse('address', null, '', 'can parse address variable with null value');
testParse('schedules', [1], []);
testParse('schedules', 1, [], 'can parse schedules variable with non array value');
testParse('schedule', 1, '');
testParse('schedule', null, '', 'can parse schedule variable with null value');
testParse('menu', '1', []);
testParse('menu', null, [], 'can parse menu variable with null value');
testParse('form', '1', 1);
testParse('form', null, 0, 'can parse form variable with null value');
it('can parse page variable', function () {
    $values = ['test' => [
        'parent_id' => 1,
        'id' => 1,
    ]];
    $parsedValues = SchemaParser::make([schema('page')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeInstanceOf(\stdClass::class);
});

it('can parse page value with not valid structure', function () {
    $values = ['test' => [
        'parent_id' => 1,
    ]];
    $parsedValues = SchemaParser::make([schema('page')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeInstanceOf(\stdClass::class);
});

it('can parse page value with not valid value', function () {
    $values = ['test' => null];
    $parsedValues = SchemaParser::make([schema('page')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeInstanceOf(\stdClass::class);
});

it('can parse pages value', function () {
    $values = ['test' => [
        'parent_id' => 1,
        'ids' => [1],
    ]];
    $parsedValues = SchemaParser::make([schema('pages')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeArray();
});

it('can parse pages value with not valid structure', function () {
    $values = ['test' => [
        'qwe' => 1,
    ]];
    $parsedValues = SchemaParser::make([schema('pages')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeArray();
});

it('can parse pages value with not valid value', function () {
    $values = ['test' => null];
    $parsedValues = SchemaParser::make([schema('pages')], $values);
    expect($parsedValues)->toBeArray();
    expect($parsedValues)->toHaveCount(1);
    expect($parsedValues)->toHaveKey('test')->and($parsedValues['test'])->toBeArray();
});

it('doesnt throws error when type is not supported', function () {
    $values = ['test' => 1];
    $val = SchemaParser::make([schema('unsupported')], $values);
    expect($val)->toBeArray();
});

it('doesnt throws error when value is not set', function () {
    $val = SchemaParser::make([schema('text')], []);
    expect($val)->toBeArray();
});
