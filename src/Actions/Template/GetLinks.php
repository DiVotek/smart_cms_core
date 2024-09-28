<?php

namespace SmartCms\Core\Actions\Template;

use Lorisleiva\Actions\Concerns\AsAction;

class GetLinks
{
    use AsAction;

    public function handle(string $key = ''): array
    {
        $reference = setting($key, []);
        $links = [];
        foreach ($reference as $link) {
            if (isset($link['entity_type']) && isset($link['entity_id'])) {
                $entity = $link['entity_type']::find($link['entity_id']);
                if ($entity) {
                    $links[$entity->name()] = $entity->route();
                }
            }
        }

        return $links;
    }
}
