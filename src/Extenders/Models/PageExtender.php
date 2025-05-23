<?php

namespace SmartCms\Core\Extenders\Models;

use SmartCms\Core\Support\Extenders\ModelExtender;

class PageExtender extends ModelExtender
{
    /** @var array<string, string> */
    protected static array $casts = [];

    /** @var array<string, Closure(Model): \Illuminate\Database\Eloquent\Relations\Relation> */
    protected static array $relations = [];

    /** @var array<string, Closure(Model): mixed> */
    protected static array $accessors = [];

    /** @var array<string, Scope> */
    protected static array $scopes = [];
}
