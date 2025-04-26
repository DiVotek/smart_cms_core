<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use SmartCms\Core\Components\Pages\Base;
use SmartCms\Core\Components\Pages\StaticPage;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Traits\HasHooks;
use Symfony\Component\HttpKernel\Attribute\Cache;

class PageHandler
{
    use HasHooks;

    public $limit = 3;

    #[Cache(public: true, maxage: 31536000, mustRevalidate: true)]
    public function __invoke(Request $request): string
    {
        $this->bindUser();
        $segments = $request->segments();
        $lang = main_lang();
        if (isset($request->lang)) {
            $this->limit++;
            $lang = $request->lang;
            array_shift($segments);
        }
        if (! app('_lang')->isFrontAvailable($lang)) {
            return redirect()->route(Route::currentRouteName(), array_merge($segments, ['lang' => main_lang()]));
        }
        $this->setLanguage($lang);
        if ($this->limit < count($segments)) {
            return abort(404);
        }
        $page = $this->findPage($segments);
        if ($page) {
            return $this->render('page', ['page' => $page]);
            // return Livewire::mount('page', ['page' => $page]);
            return Blade::renderComponent(new StaticPage($page));
        }
        $res = null;
        $this->applyHook('page.get', $res, $segments);
        if ($res) {
            return $res;
        }
        abort(404);
    }

    protected function findPage(array $segments, $parentId = null)
    {
        $slug = array_shift($segments);
        $page = Page::query()->where('slug', $slug ?? '')
            ->where('parent_id', $parentId)
            ->first();
        if (! $page) {
            return null;
        }
        if (count($segments) > 0) {
            return $this->findPage($segments, $page->id);
        }

        return $page;
    }

    private function setLanguage(string $lang): void
    {
        app()->setLocale($lang);
        Context::add('current_lang', $lang);
        app('_lang')->setCurrentLanguage($lang);
    }

    private function bindUser()
    {
        $uuid = Session::getId();
        $uuid = substr($uuid, 0, 30);
        if (! Cookie::get('uuid')) {
            Cookie::queue('uuid', $uuid, 60 * 24 * 365);
        }
    }

    private function render(string $component, array $data = [])
    {
        return Blade::renderComponent(new Base($component, $data));
    }
}
