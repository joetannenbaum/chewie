# Chewie

Chewie is a package that helps you build Text-based user interfaces (TUIs) with [Laravel Prompts](https://laravel.com/docs/prompts). It helps to reduce some of the boilerplate code and adds some helpers for alignment, animation, and more.

> [!WARNING]
> This package is currently in active development. The API is subject to change. Documentation will also improve over time.

## Installation

```
composer require joetannenbaum/chewie
```

## Usage

### Registering Renderers

```php
use App\Renderers\DemoRenderer;
use Chewie\Concerns\RegistersRenderers;

class Demo extends Prompt
{
    use RegistersRenderers;

    public function __construct()
    {
        $this->registerRenderer(DemoRenderer::class);
    }
}
```
