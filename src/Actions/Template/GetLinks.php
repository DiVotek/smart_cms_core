<?php

namespace SmartCms\Core\Actions\Template;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class GetLinks
{
    use AsAction;

    public function handle(?int $id): array
    {
        if (! $id) {
            return [];
        }
        $lang = current_lang_id();

        return Cache::get('menu_links_' . $lang . '_' . $id, function () use ($id) {
            $menu = Menu::query()->find($id);
            if ($menu) {
                $links = $this->parseLinks($menu->value);
                Cache::put('menu_links_' . $id, $links, 60 * 60 * 24);
                return $links;
            }

            return [];
        });
    }

    public function parseLinks(array $reference)
    {
        $links = [];
        $currentUrl = url()->current();
        foreach ($reference as $link) {
            if (!isset($link['type']) || !isset($link['name']) || !isset($link['children'])) {
                continue;
            }
            $route = null;
            switch ($link['type']) {
                case Page::class:
                    $page = Page::query()->find($link['id']);
                    if (!$page) {
                        break;
                    }
                    $route = $page->route();
                    break;
                case MenuSection::class:
                    $menuSection = MenuSection::query()->find($link['id']);
                    if (!$menuSection) {
                        break;
                    }
                    $page = Page::query()->find($menuSection->parent_id);
                    if (!$page) {
                        break;
                    }
                    $route = $page->route();
                    break;
                case 'custom':
                    $route = $link['url'] ?? '/';
                    break;
                default:
                    Event::dispatch('cms.menu.building', [$link, &$route]);
                    break;
            }
            if (!$route) {
                continue;
            }
            $links[] = (object) [
                'name' => $link[current_lang()]['name'] ?? $link['name'] ?? '',
                'link' => $route,
                'active' => $currentUrl === $route,
                'as_link' => $link['as_link'] ?? false,
                'children' => $this->parseLinks($link['children'] ?? []),
            ];
        }

        return $links;
    }
}
