<?php

declare(strict_types=1);

namespace SmartCms\Core\Support\Extenders;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ModelExtender
{
    /** @var array<string, string> */
    protected static array $casts = [];

    /** @var array<string, Closure(Model): \Illuminate\Database\Eloquent\Relations\Relation> */
    protected static array $relations = [];

    /** @var array<string, Closure(Model): mixed> */
    protected static array $accessors = [];

    /** @var array<string, Scope> */
    protected static array $scopes = [];

    public function addCast(string $attribute, string $castType): self
    {
        static::$casts[$attribute] = $castType;

        return $this;
    }

    public function addRelation(string $name, Closure $resolver): self
    {
        static::$relations[$name] = $resolver;

        return $this;
    }

    public function addAccessor(string $name, Closure $accessor): self
    {
        static::$accessors[$name] = $accessor;

        return $this;
    }

    public function addScope(string $name, Closure $scope): self
    {
        static::$scopes[$name] = $scope;

        return $this;
    }

    public function apply(Model $model): void
    {
        $model->mergeCasts(static::$casts);
        // if (! empty(static::$casts)) {
        //     dd(static::$casts, new static, $model);
        // }

        foreach (static::$relations as $name => $resolver) {
            $model::resolveRelationUsing($name, $resolver);
        }

        foreach (static::$accessors as $name => $accessor) {
            $model::macro('get'.ucfirst($name).'Attribute', $accessor);
        }

        foreach (static::$scopes as $id => $scope) {
            $model::addScope($id, $scope);
        }
    }

    public function getCasts(): array
    {
        return static::$casts;
    }

    public function getRelations(): array
    {
        return static::$relations;
    }

    public function getAccessors(): array
    {
        return static::$accessors;
    }

    public function getScopes(): array
    {
        return static::$scopes;
    }
}
