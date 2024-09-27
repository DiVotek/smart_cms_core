<?php

namespace  SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\GetLinks;

class Footer extends Component
{
    public array $links;

	public function __construct()
	{
		$this->links = GetLinks::run();
	}

    public function render(): View|Closure|string
    {
        $view = setting(config('settings.design.footer'), 'default-footer');
        return view('templates::layout.' . strtolower($view));
    }
}
