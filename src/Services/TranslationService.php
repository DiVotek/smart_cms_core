<?php

namespace SmartCms\Core\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Translation;

class TranslationService
{
    use AsAction;

    public string $commandSignature = 'scan:translations';

    public function handle()
    {
        $path = resource_path('views');
        $files = File::allFiles($path);
        $regex = '/_t\(\s*[\'"](.+?)[\'"]\s*\)/';
        foreach ($files as $file) {
            $contents = File::get($file->getRealPath());
            if (preg_match_all($regex, $contents, $matches)) {
                foreach ($matches[1] as $key) {
                    foreach (get_active_languages() as $lang) {
                        if (! Translation::query()->where('language_id', $lang->id)->where('key', $key)->exists()) {
                            Translation::query()->create([
                                'language_id' => $lang->id,
                                'key' => $key,
                                'value' => $key,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function asCommand(Command $command): void
    {
        $this->handle();

        $command->info('Translations scanned and saved.');
    }
}
