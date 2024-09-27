<?php

namespace SmartCms\Core\Services;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;

class TableSchema
{
    public static function getCreatedAt(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label(__('Created At'))
            ->since()
            ->toggleable()
            ->sortable();
    }

    public static function getUpdatedAt(): TextColumn
    {
        return TextColumn::make('updated_at')
            ->label(__('Updated At'))
            ->since()
            ->toggleable()
            ->sortable();
    }

    public static function getName(): TextColumn
    {
        return TextColumn::make('name')
            ->label(__('Name'))
            ->searchable();
    }

    public static function getStatus(): ToggleColumn
    {
        return ToggleColumn::make('status')
            ->label(__('Status'))
            ->sortable();
    }

    public static function getLabelStatus(): TextColumn
    {
        return TextColumn::make('status')
            ->label(__('Status'))
            ->sortable();
    }

    public static function getSlug(): TextColumn
    {
        return TextColumn::make('slug')
            ->label(__('Slug'));
    }

    public static function getEmail(): TextColumn
    {
        return TextColumn::make('email')
            ->label(__('Email'))
            ->searchable();
    }

    public static function getFirstAndLastName(): TextColumn
    {
        return TextColumn::make('firstname')
            ->label(__('Name'))
            ->formatStateUsing(function (Model $record) {
                return $record->firstname.' '.$record->lastname;
            });
    }

    public static function getPhone(): TextColumn
    {
        return TextColumn::make('phone')
            ->label(__('Phone'))
            ->searchable();
    }

    public static function getViews(): TextColumn
    {
        return TextColumn::make('views')
            ->label(__('Views'))
            ->badge()
            ->sortable()
            ->toggleable()
            ->numeric();
    }

    public static function getSorting(): TextColumn
    {
        return TextColumn::make('sorting')
            ->label(__('Sorting'))
            ->toggleable()
            ->badge()
            ->color('gray')
            ->sortable();
    }

    public static function getImage(): ImageColumn
    {
        return ImageColumn::make('image')
            ->label(__('Image'));
    }

    public static function getFilterStatus(): SelectFilter
    {
        return SelectFilter::make('status')
            ->label(__('Status'))
            ->options([
                Status::ON => __('On'),
                Status::OFF => __('Off'),
            ]);
    }

    public static function getFilterParentId(): SelectFilter
    {
        return SelectFilter::make('parent_id')
            ->label(__('Parent'))
            ->options(Category::query()->whereNull('parent_id')->pluck('name', 'id')->toArray() ?? []);
    }

    public static function getSku(): TextColumn
    {
        return TextColumn::make('sku')
            ->label(__('Sku'))
            ->numeric()
            ->toggleable()
            ->sortable();
    }

    public static function getPrice(): TextColumn
    {
        return TextColumn::make('price')
            ->label(__('Price'))
            ->numeric()
            ->money(app('currency')->code ?? '');
    }

    public static function getRating(): TextColumn
    {
        return TextColumn::make('rating')
            ->numeric()
            ->sortable();
    }

    public static function getStickerType(): TextColumn
    {
        return TextColumn::make('type')
            ->label(__('Type'))
            ->formatStateUsing(function ($record) {
                return __(Sticker::types[$record->type]);
            });
    }

    public static function getComment(): TextColumn
    {
        return TextColumn::make('comment')
            ->label(__('Comment'));
    }

    public static function getCurrency(): TextColumn
    {
        return TextColumn::make('currency_code')
            ->label(__('Currency'));
    }

    public static function getRate(): TextColumn
    {
        return TextColumn::make('rate')
            ->label(__('Rate'));
    }

    public static function getUser(): TextColumn
    {
        return TextColumn::make('user_id')
            ->label(__('User'))
            ->formatStateUsing(function (Model $record) {
                /**
                 * @var \App\Models\Cart $cart
                 */
                $cart = $record;

                return User::find($cart->user_id)->name ?? '';
            });
    }

    public static function getComission(): TextColumn
    {
        return TextColumn::make('commission')
            ->label(__('Comission'))
            ->numeric()
            ->suffix('%');
    }

    public static function getProduct(): TextColumn
    {
        return TextColumn::make('product_id')
            ->label(__('Product'))
            ->formatStateUsing(function ($record) {
                return Product::find($record->product_id)->name ?? '';
            });
    }

    public static function getSubject(): TextColumn
    {
        return TextColumn::make('subject')
            ->label(__('Subject'));
    }

    public static function getAdditional(): TextColumn
    {
        return TextColumn::make('additional')
            ->label(__('Additional'));
    }
}
