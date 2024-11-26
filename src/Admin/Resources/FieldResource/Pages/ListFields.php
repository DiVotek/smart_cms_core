<?php

namespace SmartCms\Core\Admin\Resources\FieldResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\FieldResource;

class ListFields extends ListRecords
{
   protected static string $resource = FieldResource::class;

   protected function getHeaderActions(): array
   {
      return [
         Actions\CreateAction::make(),
      ];
   }
}
