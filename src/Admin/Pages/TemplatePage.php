<?php

namespace SmartCms\Core\Admin\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use ZipArchive;

class TemplatePage extends Page
{
    protected static ?string $slug = 'templates';

    public ?array $data = [];

    public string $activeTab = 'my-templates';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Templates')
                    ->tabs([
                        Tabs\Tab::make('my-templates')
                            ->label(strans('my_templates'))
                            // ->icon('heroicon-o-template')
                            ->schema([
                                Section::make()
                                    ->schema([])
                                    ->columnSpan('full'),
                            ]),
                        Tabs\Tab::make('template-shop')
                            ->label(strans('template_shop'))
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                Section::make()
                                    ->schema([])
                                    ->columnSpan('full'),
                            ]),
                    ])
                    ->contained(false)
                    ->persistTabInQueryString()
                    ->columnSpan('full'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload_template')
                ->label(_actions('upload_template'))
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('template_file')
                        ->label(_fields('template_file'))
                        ->acceptedFileTypes(['application/zip'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->handleTemplateUpload($data['template_file']);
                }),
            Action::make('change_template')
                ->label(_actions('change_template'))
                ->icon('heroicon-o-cog-6-tooth')
                ->action(function (): void {
                    // redirect()->to('/admin/settings#template');
                }),
            Action::make('change_theme')
                ->label(_actions('change_theme'))
                ->icon('heroicon-o-paint-brush')
                ->action(function (): void {
                    // redirect()->to('/admin/settings#theme');
                }),
        ];
    }

    public function getView(): string
    {
        return 'smart_cms::admin.templates';
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('design-template');
    }

    public function getTitle(): string|Htmlable
    {
        return _nav('templates');
    }

    public static function getNavigationLabel(): string
    {
        return _nav('templates');
    }

    protected function handleTemplateUpload($file): void
    {
        try {
            $templatesPath = base_path('scms/templates');
            $zipPath = storage_path('app/public/'.$file);

            if (! File::exists($templatesPath)) {
                File::makeDirectory($templatesPath, 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath) === true) {
                // Validate template structure
                $isValid = $this->validateTemplateStructure($zip);

                if (! $isValid) {
                    Notification::make()
                        ->title(strans('invalid_template'))
                        ->danger()
                        ->send();

                    return;
                }

                $zip->extractTo($templatesPath);
                $zip->close();

                Notification::make()
                    ->title(strans('template_upload_success'))
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title(strans('template_upload_error'))
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title(strans('template_upload_error').': '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function validateTemplateStructure(ZipArchive $zip): bool
    {
        // Add validation logic here
        // Check for required files/folders in the template
        $requiredFiles = [
            'config.php',
            'views/',
            'assets/',
        ];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $filename = $stat['name'];

            foreach ($requiredFiles as $required) {
                if (str_starts_with($filename, $required)) {
                    return true;
                }
            }
        }

        return false;
    }

    #[Computed]
    public function templates(): array
    {
        $templates = [];
        $templatesPath = base_path('scms/templates');

        if (! File::exists($templatesPath)) {
            return [];
        }

        foreach (File::directories($templatesPath) as $template) {
            $templateName = basename($template);
            $configFile = $template.'/config.php';
            $config = File::exists($configFile) ? include ($configFile) : [];
            $thumbnail = File::exists($template.'/thumbnail.png')
                ? asset('scms/templates/'.$templateName.'/thumbnail.png')
                : asset('images/default-template.png');

            $templates[] = [
                'name' => $config['name'] ?? $templateName,
                'description' => $config['description'] ?? _fields('no_description'),
                'thumbnail' => $thumbnail,
                'path' => $templateName,
            ];
        }

        return $templates;
    }
}
