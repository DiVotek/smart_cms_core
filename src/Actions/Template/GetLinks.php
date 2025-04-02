<?php

namespace SmartCms\Core\Actions\Template;

use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Traits\HasHooks;

/**
 * Class GetLinks
 *
 * Gets links for the given menu.
 */
class GetLinks
{
    use AsAction;
    use HasHooks;

    /**
     * Gets links for the given menu.
     *
     * @param  int|null  $id  The ID of the menu.
     * @return array The links for the given menu.
     */
    public function handle(?int $id): array
    {
        if (! $id) {
            return [];
        }
        $lang = current_lang_id();

        // return Cache::remember('menu_links_' . $lang . '_' . $id, 3600, function () use ($id) {
        $menu = Menu::query()->find($id);
        if ($menu) {
            $links = $this->parseLinks($menu->value);
            Cache::put('menu_links_'.$id, $links, 60 * 60 * 24);

            return $links;
        }

        return [];
        // });
    }

    /**
     * Parses the links for the given reference.
     *
     * @param  array  $reference  The reference to parse the links for.
     * @return array The parsed links.
     */
    public function parseLinks(array $reference)
    {
        $links = [];
        $currentUrl = url()->current();
        foreach ($reference as $link) {
            if (! isset($link['type']) || ! isset($link['name']) || ! isset($link['children'])) {
                continue;
            }
            $route = null;
            $name = $link[current_lang()]['name'] ?? $link['name'] ?? '';
            switch ($link['type']) {
                case Page::class:
                    $page = Page::query()->find($link['id']);
                    if (! $page) {
                        break;
                    }
                    $isModified = $link['is_modified'] ?? 0;
                    // dd($isModified, $name, $link, $page->name());
                    if (! $isModified) {
                        $name = $page->name();
                    }
                    $route = $page->route();
                    break;
                case MenuSection::class:
                    $menuSection = MenuSection::query()->find($link['id']);
                    if (! $menuSection) {
                        break;
                    }
                    $page = Page::query()->find($menuSection->parent_id);
                    if (! $page) {
                        break;
                    }
                    $isModified = $link['is_modified'] ?? 0;
                    if (! $isModified) {
                        $name = $page->name();
                    }
                    $route = $page->route();
                    break;
                case 'custom':
                    $route = $link['url'] ?? '/';
                    break;
                case 'text':
                    $route = url('/');
                default:
                    self::applyHook('menu.building', $route, $link);
                    break;
            }
            if (! $route) {
                continue;
            }
            $links[] = (object) [
                'name' => $name,
                'link' => $route,
                'active' => $currentUrl === $route,
                'as_link' => $link['type'] !== 'text',
                'children' => $this->parseLinks($link['children'] ?? []),
            ];
        }

        return $links;
    }
}
