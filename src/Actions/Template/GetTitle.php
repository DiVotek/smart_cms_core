<?php

namespace SmartCms\Core\Actions\Template;

use Lorisleiva\Actions\Concerns\AsAction;

class GetTitle
{
    use AsAction;

    public function handle(array $options = []): string
    {
        $title = '';
        if (isset($options[current_lang()]) && isset($options[current_lang()]['title'])) {
            $title = $options[current_lang()]['title'];
        }

        return $title;
    }
}
