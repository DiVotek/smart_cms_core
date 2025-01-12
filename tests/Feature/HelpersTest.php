<?php

use Illuminate\Support\Collection;

test('logo', function () {
   expect(logo())->toBeString();
});

test('phones', function () {
   expect(phones())->toBeArray();
});

test('emails', function () {
   expect(emails())->toBeArray();
});

test('socials', function () {
   expect(socials())->toBeArray();
});

test('socal_names', function () {
   expect(social_names())->toBeArray();
});

test('template', function () {
   expect(template())->toBeString();
});

test('company_name', function () {
   expect(company_name())->toBeString();
});

test('addressess', function () {
   expect(addresses())->toBeArray();
});

test('schedules', function () {
   expect(schedules())->toBeArray();
});

test('company_info', function () {
   expect(company_info())->toBeArray();
});

test('format_phone', function () {
   expect(format_phone('1234567890'))->toBeString();
});

test('no_image', function () {
   expect(no_image())->toBeString();
});


test('main_lang_id', function () {
   expect(main_lang_id())->toBeInt();
});

test('main_lang', function () {
   expect(main_lang())->toBeString();
});

test('current_lang_id', function () {
   expect(current_lang_id())->toBeString();
});

test('current_lang', function () {
   expect(current_lang())->toBeString();
});

test('is_multi_lang', function () {
   expect(is_multi_lang())->toBeBool();
});

test('get_active_languages', function () {
   expect(get_active_languages())->toBeInstanceOf(Collection::class);
});
