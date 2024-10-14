<?php

namespace SmartCms\Core\Actions\Template;

use Lorisleiva\Actions\Concerns\AsAction;

class GetLinks
{
    use AsAction;

    public function handle(array $reference): array
    {
        $links = [];
        foreach ($reference as $link) {
            if (isset($link['entity_type']) && isset($link['entity_id'])) {
                $entity = $link['entity_type']::find($link['entity_id']);
                if ($entity) {
                    $newLink = [
                        'name' => $entity->name(),
                        'slug' => $entity->route(),
                        'children' => [],
                    ];
                    if (isset($link['children'])) {
                        foreach ($link['children'] as $child) {
                            $childEntity = $child['entity_type']::find($child['entity_id']);
                            if ($childEntity) {
                                $newLink['children'][] = [
                                    'name' => $childEntity->name(),
                                    'slug' => $childEntity->route(),
                                ];
                            }
                        }
                    }
                    $links[] = $newLink;
                }
            }
        }

        return $links;
    }
}
