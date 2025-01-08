<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use SmartCms\Core\Components\Pages\StaticPage;
use Symfony\Component\HttpKernel\Attribute\Cache;

class PageHandler
{
    public $limit = 3;

    #[Cache(public: true, maxage: 31536000, mustRevalidate: true)]
    public function __invoke(Request $request): string
    {
        $this->bindUser();
        $segments = $request->segments();
        if (isset($request->lang)) {
            $this->limit++;
            $this->setLanguage($request->lang);
            array_shift($segments);
        }
        if ($this->limit < count($segments)) {
            return abort(404);
        }
        $page = $this->findPage($segments);
        if ($page) {
            return Blade::renderComponent(new StaticPage($page));
        }
        $res = Event::dispatch('cms.page.get', [$segments]);
        if ($res && is_array($res) && count($res) > 0) {
            return $res[0];
        }
        abort(404);
    }

    protected function findPage(array $segments, $parentId = null)
    {
        $slug = array_shift($segments);
        $page = config('shared.page_model')::query()->where('slug', $slug ?? '')
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
    }

    private function bindUser()
    {
        $uuid = Session::getId();
        $uuid = substr($uuid, 0, 30);
        if (! Cookie::get('uuid')) {
            Cookie::queue('uuid', $uuid, 60 * 24 * 365);
        }
    }
}
