<?php

namespace App\Actions;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Lorisleiva\Actions\Concerns\AsAction;

class ModuleTitleSchema
{
    use AsAction;

    public function handle(): array
    {
        $fields = [
            TextInput::make('value.'.main_lang().'.title')->label(_fields('title'))->required(),
        ];
        if (is_multi_lang()) {
            foreach (get_active_languages() as $lang) {
                if ($lang->id == main_lang_id()) {
                    continue;
                }
                $fields[] = TextInput::make('value.'.$lang->slug.'.title')->label(_fields('title').$lang->name);
            }
        }

        return [
            Fieldset::make(__('Heading'))->schema([
                ...$fields,
                Group::make([
                    Toggle::make('value.use_page_heading')->label(_fields('use_page_heading'))->default(true)->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('value.use_page_name', false);
                        }
                    }),
                    Toggle::make('value.use_page_name')->label(_fields('use_page_name'))->default(false)->reactive()->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('value.use_page_heading', false);
                        }
                    }),
                ])->columns(2),
                Group::make([
                    Radio::make('value.heading_type')
                        ->options([
                            'h1' => 'H1',
                            'h2' => 'H2',
                            'h3' => 'H3',
                            'none' => 'None',
                        ])
                        ->required()
                        ->default('h2')->inline(),
                ])->columns(2),
            ])->columns(1),
        ];

        return $fields;
    }
}
