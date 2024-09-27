<?php

namespace SmartCms\Core\Components\Misc;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Gtm extends Component
{
    public string $gtm;

    public function __construct()
    {
        $this->gtm = setting(config('settings.gtm'), '');
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            @if ($gtm)
                <noscript>
                    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display: none; visibility: hidden"></iframe>
                </noscript>
                <script async>
                    (function(w, d, s, l, i) {
                        w[l] = w[l] || [];
                        w[l].push({
                            'gtm.start': new Date().getTime(),
                            event: 'gtm.js',
                        });
                        var f = d.getElementsByTagName(s)[0],
                            j = d.createElement(s),
                            dl = l != 'dataLayer' ? '&l=' + l : '';
                        j.async = true;
                        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                        f.parentNode.insertBefore(j, f);
                    })(window, document, 'script', 'dataLayer', '{{ $gtm }}');
                </script>
            @endif
        blade;
    }
}
