<?php

namespace SmartCms\Core\Traits;

use Faker\Factory as FakerFactory;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

trait HasSlug
{
    public function initializeHasSlug()
    {
        $this->mergeFillable([$this->getSlugColumn()]);
    }

    protected static function bootHasSlug(): void
    {
        static::booting(function (Model $model) {
            $model->mergeFillable([$model->getSlugColumn()]);
        });
    }

    public function getSlugColumn(): string
    {
        return property_exists($this, 'slugColumn') ? $this->slugColumn : 'slug';
    }

    public function slugMigrationField(Blueprint $table): void
    {
        $table->string($this->getSlugColumn())->unique()->index();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSlug(Builder $query, string $slug)
    {
        return $query->where($this->getSlugColumn(), $slug);
    }

    public function slugFilamentField(): TextInput
    {
        return TextInput::make($this->getSlugColumn())
            ->string()
            ->readOnly()
            ->required()
            ->helperText(__('Slug will be generated automatically from title of any language'))
            ->hintAction(
                Action::make(__('Clear slug'))
                    ->requiresConfirmation()
                    ->action(function (Set $set) {
                        $set('slug', null);
                    })
            );
    }

    public function fakeSlug(): string
    {
        $slug = fake()->slug();
        while (static::where($this->getSlugColumn(), $slug)->exists()) {
            $slug = FakerFactory::create()->unique()->slug();
        }

        return $slug;
    }
}
