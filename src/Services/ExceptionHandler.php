<?php

namespace SmartCms\Core\Services;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionHandler extends Handler
{
    public function render($request, Throwable $exception)
    {
        $template_errors = resource_path('views/errors/error.blade.php');
        if (config('app.debug') || ! File::exists($template_errors)) {
            return parent::render($request, $exception);
        }
        $status = ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : 500;
        $content = File::get($template_errors);
        $view = Blade::render($content, ['exception' => $exception, 'status' => $status]);

        return response($view, $status);
    }
}
