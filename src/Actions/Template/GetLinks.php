<?php

namespace SmartCms\Core\Actions\Template;

use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Menu;

class GetLinks
{
    use AsAction;

    public function handle(?int $id): array
    {
        if (! $id) {
            return [];
        }

        return Cache::get('menu_links_'.$id, function () use ($id) {
            $menu = Menu::query()->find($id);
            if ($menu) {
                $links = $this->parseLinks($menu->value);
                Cache::put('menu_links_'.$id, $links, 60 * 60 * 24);

                return $links;
            }

            return [];
        });
    }

    public function parseLinks(array $reference)
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
