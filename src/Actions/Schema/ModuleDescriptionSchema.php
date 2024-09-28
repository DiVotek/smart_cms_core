<?php

namespace App\Actions;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Lorisleiva\Actions\Concerns\AsAction;

class ModuleDescriptionSchema
{
    use AsAction;

    public function handle(): array
    {
        $fields = [
            Textarea::make('value.'.main_lang().'.description')->label('Description'),
        ];
        if (is_multi_lang()) {
            foreach (get_active_languages() as $lang) {
                if ($lang->id == main_lang_id()) {
                    continue;
                }
                $fields[] = Textarea::make('value.'.$lang->slug.'.description')->label('Description '.$lang->name);
            }
        }

        return [Fieldset::make('Description')->schema([
            ...$fields,
            Group::make([
                Toggle::make('value.use_page_description')
                    ->label(__('Use page description'))->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('value.is_summary', false);
                        }
                    }),
                Toggle::make('value.use_page_summary')
                    ->label(__('Use page summary'))->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('value.is_description', false);
                        }
                    }),
            ])->columns(2),
        ])->columns(1)];

        return $fields;
    }
}
