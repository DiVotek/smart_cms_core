<?php

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;

function schema(string $type): array
{
   return [
      'name' => 'test',
      'type' => $type
   ];
}

function testType(string $type, $instance)
{
   it("can build $type variable", function () use ($type, $instance) {
      $field = ArrayToField::make(schema($type));
      $filamentFields = Builder::make($field);
      expect($filamentFields)->toBeArray();
      expect($filamentFields)->toHaveCount(1);
      expect($filamentFields[0])->toBeInstanceOf($instance);
   });
}

testType('text', TextInput::class);
testType('number', TextInput::class);
testType('bool', Toggle::class);
// testType('image', FileUpload::class); cant test because of the optimize method
// testType('file', FileUpload::class); @todo
testType('heading', Fieldset::class);
testType('description', Grid::class);
testType('socials', Select::class);
testType('phone', Select::class);
testType('phones', Select::class);
testType('email', Select::class);
testType('emails', Select::class);
testType('address', Select::class);
testType('addresses', Select::class);
testType('schedule', Select::class);
testType('schedules', Select::class);
testType('menu', Select::class);
testType('form', Select::class);
testType('page', Fieldset::class);
testType('pages', Fieldset::class);
// testType('array', Repeater::class);
it("can build array variable", function () {
   $schema = [
      'name' => 'test',
      'type' => 'array',
      'schema' => [
         schema('text'),
         schema('number'),
      ]
   ];
   $field = ArrayToField::make($schema);
   $filamentFields = Builder::make($field);
   expect($filamentFields)->toBeArray();
   expect($filamentFields)->toHaveCount(1);
   expect($filamentFields[0])->toBeInstanceOf(Repeater::class);
});

it("cant build array without schema", function () {
   $schema = [
      'name' => 'test',
      'type' => 'array',
   ];
   $field = ArrayToField::make($schema);
   Builder::make($field);
})->throws(\Exception::class);

it("can build variable without type", function () {
   $schema = [
      'name' => 'test',
   ];
   $field = ArrayToField::make($schema);
   $filamentFields = Builder::make($field);
   expect($filamentFields)->toBeArray();
   expect($filamentFields)->toHaveCount(1);
   expect($filamentFields[0])->toBeInstanceOf(TextInput::class);
});

it("throws error when type is not supported", function () {
   $schema = [
      'name' => 'test',
      'type' => 'unsupported',
   ];
   $field = ArrayToField::make($schema);
   Builder::make($field);
})->throws(\Exception::class);
