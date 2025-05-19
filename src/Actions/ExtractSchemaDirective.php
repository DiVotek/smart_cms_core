<?php

namespace SmartCms\Core\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ExtractSchemaDirective
{
    use AsAction;
    /**
     * @param  string  $viewPath  path/to/view.blade.php
     * @return array<array{name:string,type:string,schema?:array}>
     */
    public function handle(string $viewPath): array
    {
        $flat = ParseSchemaDirective::run($viewPath);
        $roots   = [];
        $nested  = [];

        foreach ($flat as $full => $type) {
            if (strpos($full, '.') === false) {
                $roots[$full] = [
                    'name' => $full,
                    'type' => $type,
                ];
            } else {
                $parts  = explode('.', $full);
                $parent = array_shift($parts);
                $child  = end($parts);
                $nested[$parent][$child] = $type;
            }
        }

        $out = [];
        foreach ($roots as $name => $def) {
            if (isset($nested[$name])) {
                $children = [];
                foreach ($nested[$name] as $cName => $cType) {
                    $children[] = [
                        'name' => $cName,
                        'type' => $cType,
                    ];
                }
                $def['schema'] = $children;
            }
            $out[] = $def;
        }

        return $out;
    }
}
