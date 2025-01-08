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
        $lang = current_lang_id();

        return Cache::get('menu_links_'.$lang.'_'.$id, function () use ($id) {
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
                    $links[] = (object) [
                        'name' => $entity->name(),
                        'slug' => $entity->route(),
                        'children' => $this->parseLinks($link['children'] ?? []),
                    ];
                }
            }
        }

        return $links;
    }
}
