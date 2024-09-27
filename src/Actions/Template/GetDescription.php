<?php

namespace SmartCms\Core\Actions\Template;

use Lorisleiva\Actions\Concerns\AsAction;

class GetDescription
{
    use AsAction;

    public function handle(array $options = []): string
    {
        $title = '';
        if (isset($options[current_lang()]) && isset($options[current_lang()]['description'])) {
            $title = $options[current_lang()]['description'];
        }

        return $title;
    }
}
