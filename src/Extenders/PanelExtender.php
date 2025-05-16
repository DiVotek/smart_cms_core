<?php

declare(strict_types=1);

namespace SmartCms\Core\Extenders;

class PanelExtender
{
    /**
     * @var array<string>
     */
    private array $resources = [];

    /**
     * @var array<array{label:string, url:string, icon:string}>
     */
    private array $menus = [];

    /**
     * @var array<string, string>
     */
    private array $pages = [];

    /**
     * @var array<string, string>
     */
    private array $settingsPages = [];

    /**
     * @var array<string>
     */
    private array $widgets = [];

    /**
     * @var array<string>
     */
    private array $plugins = [];

    /**
     * @var array<string>
     */
    private array $profileNotifications = [];

    public function addResource(string $resourceClass): self
    {
        $this->resources[] = $resourceClass;

        return $this;
    }

    public function addMenu(string $label, string $url, string $icon = ''): self
    {
        $this->menus[] = compact('label', 'url', 'icon');

        return $this;
    }

    public function addPage(string $slug, string $pageClass): self
    {
        $this->pages[$slug] = $pageClass;

        return $this;
    }

    public function addSettingsPage(string $key, string $settingsPageClass): self
    {
        $this->settingsPages[$key] = $settingsPageClass;

        return $this;
    }

    public function addWidget(string $widgetClass): self
    {
        $this->widgets[] = $widgetClass;

        return $this;
    }

    public function addPlugin(string $pluginClass): self
    {
        $this->plugins[] = $pluginClass;

        return $this;
    }

    public function addProfileNotification(string $notificationClass): self
    {
        $this->profileNotifications[] = $notificationClass;

        return $this;
    }

    public function getResources(): array
    {
        return $this->resources;
    }

    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getSettingsPages(): array
    {
        return $this->settingsPages;
    }

    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getProfileNotifications(): array
    {
        return $this->profileNotifications;
    }
}
