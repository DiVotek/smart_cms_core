<?php

namespace SmartCms\Core\Services;

class Helper
{
    public static function getLabelFromField(string $field): string
    {
        $field = preg_replace('/(?<!^)([A-Z])/', ' $1', $field);
        $field = explode('_', $field);
        $field = implode(' ', $field);

        return __(ucfirst(strtolower($field)));
    }

    public static function getVariableSchema(array|string $var, string $prefix = ''): array
    {
        if (is_array($var) && isset($var['name']) && isset($var['type'])) {
            return $var;
        }
        $variable = [
            'name' => $var,
        ];
        if (str_contains('heading', $var)) {
            $variable['type'] = VariableTypes::HEADING->value;
        } elseif (str_contains('description', $var)) {
            $variable['type'] = VariableTypes::DESCRIPTION->value;
        } elseif (str_contains('image', $var)) {
            $variable['type'] = VariableTypes::IMAGE->value;
        } elseif (str_contains('status', $var)) {
            $variable['type'] = VariableTypes::BOOLEAN->value;
        } elseif (str_contains('links', $var)) {
            $variable['type'] = VariableTypes::LINKS->value;
        } elseif (str_contains($var, 'phones')) {
            $variable['type'] = VariableTypes::PHONES->value;
        } elseif (str_contains($var, 'phone')) {
            $variable['type'] = VariableTypes::PHONE->value;
        } elseif (str_contains($var, 'email')) {
            $variable['type'] = VariableTypes::EMAIL->value;
        } elseif (str_contains($var, 'addresses')) {
            $variable['type'] = VariableTypes::ADDRESSES->value;
        } elseif (str_contains($var, 'address')) {
            $variable['type'] = VariableTypes::ADDRESS->value;
        } elseif (str_contains($var, 'schedules')) {
            $variable['type'] = VariableTypes::SCHEDULES->value;
        } elseif (str_contains($var, 'schedule')) {
            $variable['type'] = VariableTypes::SCHEDULE->value;
        } elseif (str_contains($var, 'socials')) {
            $variable['type'] = VariableTypes::SOCIALS->value;
        } elseif (str_contains($var, 'pages')) {
            $variable['type'] = VariableTypes::PAGES->value;
        } elseif (str_contains($var, 'page')) {
            $variable['type'] = VariableTypes::PAGE->value;
        } elseif (str_contains($var, 'form')) {
            $variable['type'] = VariableTypes::FORM->value;
        }
        if (! isset($variable['type'])) {
            $variable['type'] = VariableTypes::TEXT->value;
        }

        return $variable;
    }

    public static function parseVariable(array|string $var, string $prefix = ''): array
    {
        if (! is_array($var) || ! isset($var['type'])) {
            $var = self::getVariableSchema($var, $prefix);
        }

        return VariableTypes::fromType($var['type'])->toFilamentField($var, $prefix);
    }
}
