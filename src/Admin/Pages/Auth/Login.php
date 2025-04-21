<?php

namespace SmartCms\Core\Admin\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as AuthLogin;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use SmartCms\Core\Services\AdminNotification;

class Login extends AuthLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        if (! Filament::auth()->attempt([
            'username' => $data['username'],
            'password' => $data['password'],
        ], $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
        $this->checkVersion();
        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();
        cookie()->queue(
            cookie('maintenance_bypass', 'true', 60 * 24 * 7)
        );

        return app(LoginResponse::class);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('username')
                            ->label(_fields('username'))
                            ->required(),
                        TextInput::make('password')
                            ->label(_fields('password'))
                            ->password()
                            ->required(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),

        ];
    }

    protected function checkVersion()
    {
        $url = 'https://api.github.com/repos/DiVotek/smart_cms_core/releases/latest';
        try {
            $response = Http::timeout(5)->get($url);
        } catch (\Exception $e) {
            return;
        }
        if ($response->successful()) {
            $release = $response->json();
            $version = $release['tag_name'];
            if ($version > _settings('version', 0)) {
                setting([
                    sconfig('version') => $version,
                ]);
                AdminNotification::make()
                    ->title('New version of Smart CMS is available. Please update your system.')
                    ->info()
                    ->sendToAll();
            }
        }
    }
}
