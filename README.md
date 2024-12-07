# Chewie

Chewie is a package that helps you build text-based user interfaces (TUIs) with [Laravel Prompts](https://laravel.com/docs/prompts). It helps to reduce some of the boilerplate code and adds some helpers for alignment, animation, and more.

> [!WARNING]
> This package is currently in active development. The API is subject to change. Documentation will also improve over time.

## Installation

```
composer require joetannenbaum/chewie
```

## Registering Renderers

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

You can also tell Chewie that all of your renderers live within a specific namespace, then Chewie will resolve your renderers automatically.

For example, if you call the following in your `AppServiceProvider`:

```php
use Chewie\Renderer;

class AppServiceProvider
{
    public function boot()
    {
        Renderer::setNamespace('App\\Renderers');
    }
}
```

Then you can simply do the following when registering renderers. Chewie assumes your renderer class will be your app class + `Renderer`:

```php
use Chewie\Concerns\RegistersRenderers;

class Demo extends Prompt
{
    use RegistersRenderers;

    public function __construct()
    {
        // Will register App\Renderers\DemoRenderer
        $this->registerRenderer();
    }
}
```

## Drawing Art

You can easily print ASCII art from a file out to the terminal in your renderer:

```php
use Chewie\Concerns\DrawsArt;
use Laravel\Prompts\Themes\Default\Renderer;

class DemoRenderer extends Renderer
{
    use DrawsArt;

    public function __invoke(Demo $prompt): string
    {
        // Returns a collection of the lines from your art,
        // assumes a ".txt" extension
        $this->artLines(storage_path('my-art/horse'))
            ->each($this->line(...));

        return $this;
    }
}
```

You can also tell Chewie where all of your art files live:

```php
use Chewie\Art;

class AppServiceProvider
{
    public function boot()
    {
        Art::setDirectory(storage_path('my-art'));
    }
}
```

which allows you to simplify the `artLines` call to:

```php
use Chewie\Concerns\DrawsArt;
use Laravel\Prompts\Themes\Default\Renderer;

class DemoRenderer extends Renderer
{
    use DrawsArt;

    public function __invoke(Demo $prompt): string
    {
        $this->artLines('horse')->each($this->line(...));

        return $this;
    }
}
```

## Alignment

Chewie comes with methods that help align content within the terminal.

```php
use Chewie\Concerns\Aligns;
use Laravel\Prompts\Themes\Default\Renderer;

class DemoRenderer extends Renderer
{
    use Aligns;

    public function __invoke(Demo $prompt): string
    {
        $width = $prompt->terminal()->cols();
        $height = $prompt->terminal()->lines();

        $lines = [
            'Hello!',
            'My name is Joe',
        ];

        $this->centerHorizontally($lines, $width)
            ->each($this->line(...));

        $this->centerVertically($lines, $height)
            ->each($this->line(...));

        $this->center($lines, $width, $height)
            ->each($this->line(...));

        $this->line($this->spaceBetween($width, ...$lines));

        $this->pinToBottom($height, function() {
            $this->newLine();
            $this->line('This is pinned to the bottom!');
        });

        return $this;
    }
}
```
