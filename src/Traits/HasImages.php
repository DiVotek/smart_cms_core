<?php

namespace SmartCms\Core\Traits;

use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

trait HasImages
{
    public function initializeHasImages()
    {
        $this->mergeCasts([
            $this->getImagesColumn() => 'array',
        ]);
    }

    protected static function bootHasImages(): void
    {
        static::booting(function (Model $model) {
            $model->mergeFillable([$model->getImagesColumn()]);
            $model->mergeCasts([$model->getImagesColumn() => 'array']);
        });
    }

    public function getImagesColumn(): string
    {
        return property_exists($this, 'imagesColumn') ? $this->imagesColumn : 'images';
    }

    public function imagesMigrationField(Blueprint $table): void
    {
        $table->json($this->getImagesColumn())->nullable();
    }

    public function imagesFilamentField(): FileUpload
    {
        return FileUpload::make($this->getImagesColumn())
            ->label(__('Images'))
            ->helperText(__('Upload multiple images for the model.'))
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->openable()
            ->optimize('webp')
            ->multiple()
            ->disk('public');
    }

    /**
     * @return string
     */
    public function fakeImages(): array
    {
        return [
            fake()->imageUrl(150, 150, format: 'jpeg'),
            fake()->imageUrl(150, 150, format: 'jpeg'),
            fake()->imageUrl(150, 150, format: 'jpeg'),
        ];
    }

    protected function image(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->images[0] ?? 'https://via.placeholder.com/150',
        );
    }
}
