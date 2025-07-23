<?php

namespace SmartCms\Core\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ProvideDefaultVariables
{
    use AsAction;

    /**
     * @param  array<string,string>  $schema
     * @return array<string,mixed>
     */
    public function handle(array $schema): array
    {
        $defaults = [];

        foreach ($schema as $name => $type) {
            $var = explode('.', $name)[0];
            if (array_key_exists($var, $defaults)) {
                continue;
            }

            $defaults[$var] = match ($type) {
                'text', 'textarea', 'wysiwyg', 'heading', 'description', 'email', 'phone' => '',
                'number' => 0,
                'bool' => false,
                'array' => [],
                'image', 'file' => null,
                default => null,
            };
        }

        return $defaults;
    }
}
