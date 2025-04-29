<?php

namespace SmartCms\Core\Components;

use Closure;
use Gregwar\Image\Image as ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public int $maxHeight;

    public string $placeholder;

    protected array $breakpoints = [
        'mobile' => 480,
        'tablet' => 768,
        'desktop' => 1200,
        '2k' => 1920,
    ];

    public function __construct(int $maxHeight = 600)
    {
        $this->maxHeight = $maxHeight;
        $this->placeholder = no_image();
    }

    public function render(): View|Closure|string
    {
        return function (array $data) {
            if ($this->shouldUseImagePlaceholder($data)) {
                $this->setPlaceholderAttributes();

                return $this->renderTemplate();
            }

            $src = $data['attributes']['src'];
            if (! str_contains($src, 'http')) {
                $src = validateImage($src);
                $this->attributes->setAttributes(array_merge($this->attributes->getAttributes(), ['src' => $src]));
            }

            if ($this->isSvgImage($src)) {
                // $src = $this->isLocalUrl($src) ? $this->normalizePath($src) : $src;
                // $this->updateSrcAttribute($src);
                return $this->renderTemplate();
            }

            if ($this->isLocalUrl($src)) {
                $this->processLocalImage($src);
            }

            return $this->renderTemplate();
        };
    }

    /**
     * Check if we should use a placeholder instead of an actual image
     */
    private function shouldUseImagePlaceholder(array $data): bool
    {
        return ! isset($data['attributes']['src']) || strlen($data['attributes']['src']) === 0;
    }

    /**
     * Set placeholder as the source attribute
     */
    private function setPlaceholderAttributes(): void
    {
        $attributes = array_merge($this->attributes->getAttributes(), ['src' => $this->placeholder]);
        $this->attributes->setAttributes($attributes);
    }

    /**
     * Check if the given source URL is an SVG image
     */
    private function isSvgImage(string $src): bool
    {
        return str_ends_with(strtolower($src), '.svg');
    }

    /**
     * Process local image - normalize path, resize, and convert to webp
     */
    private function processLocalImage(string $src): void
    {
        if (! _settings('resize.enabled')) {
            return;
        }
        $localPath = $this->getLocalPathFromUrl($src);

        $width = $this->attributes->get('width', $this->maxHeight);

        $srcset = $this->generateSrcset($localPath, (int) $width, (int) $this->maxHeight);
        if (! empty($srcset)) {
            $attributes = array_merge($this->attributes->getAttributes(), [
                'srcset' => $srcset,
                'sizes' => $this->generateSizes($width),
            ]);
            $this->attributes->setAttributes($attributes);
        }
    }

    /**
     * Check if the URL is local (points to the app's domain)
     */
    private function isLocalUrl(string $src): bool
    {
        // Absolute path or relative path without protocol is local
        if (str_starts_with($src, '/') || (! str_contains($src, '://') && ! str_starts_with($src, '//'))) {
            return true;
        }

        // Check if URL host matches current host
        if (str_contains($src, '://')) {
            $currentHost = request()->getHttpHost();
            $urlParts = parse_url($src);
            $currentHostParts = explode(':', $currentHost);
            $currentHostWithoutPort = $currentHostParts[0];

            return isset($urlParts['host']) &&
                ($urlParts['host'] === $currentHost || $urlParts['host'] === $currentHostWithoutPort);
        }

        return false;
    }

    /**
     * Get the local filesystem path from a URL
     */
    private function getLocalPathFromUrl(string $src): string
    {
        return parse_url($src, PHP_URL_PATH);
    }

    /**
     * Normalize path by removing double slashes and handling storage prefix
     */
    private function normalizePath(string $src): string
    {
        if (str_starts_with($src, '/')) {
            $src = substr($src, 1);
        }

        if (! str_contains($src, 'storage') && ! str_contains($src, 'http')) {
            $src = 'storage/'.$src;
        }

        // Normalize all double slashes
        return preg_replace('#/+#', '/', $src);
    }

    /**
     * Update the src attribute in the component attributes
     */
    private function updateSrcAttribute(string $src): void
    {
        $attributes = array_merge($this->attributes->getAttributes(), [
            'src' => $src,
        ]);
        $this->attributes->setAttributes($attributes);
    }

    /**
     * Render the Blade template
     */
    private function renderTemplate(): string
    {
        return <<<'blade'
                <img {{$attributes}} />
        blade;
    }

    /**
     * Generate srcset attribute with different image sizes
     */
    private function generateSrcset(string $localPath, int $requestedWidth, int $maxWidth): string
    {
        $srcset = [];
        $widths = $this->calculateBreakpointWidths($requestedWidth, $maxWidth);
        $originalWidth = $this->attributes->get('width', $requestedWidth);
        $originalHeight = $this->attributes->get('height', $this->maxHeight);
        $aspectRatio = $originalHeight / $originalWidth;
        $useHeight = _settings('resize.two_sides', true);
        $isAutoscale = _settings('resize.autoscale', true);
        if (! file_exists(public_path($localPath))) {
            $localPath = '/storage'._settings('no_image', '/no-image.webp');
        }
        foreach ($widths as $width) {
            try {
                if ($useHeight) {
                    $height = round($width * $aspectRatio);
                } else {
                    $height = null;
                }
                $optimizedImage = (new ImageService(public_path($localPath)))->resize(height: $height, width: $width, rescale: $isAutoscale)->setFallback(public_path('/storage'._settings('no_image', '/no-image.webp')))->webp();
                $srcset[] = asset($optimizedImage)." {$width}w";
            } catch (\Exception $e) {
                continue;
            }
        }

        return implode(', ', $srcset);
    }

    /**
     * Calculate proportional widths for each breakpoint
     */
    private function calculateBreakpointWidths(int $requestedWidth, int $maxWidth): array
    {
        $widths = [];
        $originalAspectRatio = $requestedWidth / $maxWidth;

        $sortedBreakpoints = $this->breakpoints;
        asort($sortedBreakpoints);

        $isSmallImage = $requestedWidth < $maxWidth;

        foreach ($sortedBreakpoints as $device => $breakpoint) {
            if ($isSmallImage) {
                $calculatedWidth = min($requestedWidth, round($breakpoint * $originalAspectRatio));
                if ($calculatedWidth > 150) {
                    $widths[$device] = $calculatedWidth;
                }
            } else {
                $calculatedWidth = min($breakpoint, $maxWidth);
                $widths[$device] = $calculatedWidth;
            }
        }

        if (! in_array($requestedWidth, $widths)) {
            $widths['requested'] = $requestedWidth;
        }

        asort($widths);

        return array_unique($widths);
    }

    /**
     * Generate sizes attribute based on breakpoints
     */
    private function generateSizes(int $width): string
    {
        $sizes = [];

        $sizes[] = "(max-width: {$this->breakpoints['mobile']}px) 100vw";
        $sizes[] = "(max-width: {$this->breakpoints['tablet']}px) 90vw";
        $sizes[] = "(max-width: {$this->breakpoints['desktop']}px) 80vw";
        $sizes[] = "{$width}px";

        return implode(', ', $sizes);
    }
}
