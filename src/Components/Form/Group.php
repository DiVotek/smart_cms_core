<?php

namespace SmartCms\Core\Components\Form;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Group extends Component
{
    public function render()
    {
        return Cache::rememberForever('scms_form_group_component', function () {
            if (view()->exists('templates::'.template().'.form.group')) {
                return view('templates::'.template().'.form.group');
            }

            return <<<'blade'
            <div {{ $attributes }}>
               {{ $slot }}
            </div>
         blade;
        });
    }
}
