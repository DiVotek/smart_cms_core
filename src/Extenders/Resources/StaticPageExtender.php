<?php

namespace SmartCms\Core\Extenders\Resources;

use SmartCms\Core\Support\Extenders\ResourceExtender;

class StaticPageExtender extends ResourceExtender
{
    /** @var Closure[] */
    public static array $formFields = [];

    /** @var Closure[] */
    public static array $tableColumns = [];

    /** @var Closure[] */
    public static array $filters = [];

    /** @var array<string, string> */
    public static array $pages = [];

    /** @var Closure[] */
    public static array $actions = [];
}
