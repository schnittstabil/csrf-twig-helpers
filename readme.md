# Csrf\Twig\Helpers [![Build Status](https://travis-ci.org/schnittstabil/csrf-twig-helpers.svg?branch=master)](https://travis-ci.org/schnittstabil/csrf-twig-helpers) [![Coverage Status](https://coveralls.io/repos/github/schnittstabil/csrf-twig-helpers/badge.svg?branch=master)](https://coveralls.io/github/schnittstabil/csrf-twig-helpers?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/schnittstabil/csrf-twig-helpers/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/schnittstabil/csrf-twig-helpers/?branch=master) [![Code Climate](https://codeclimate.com/github/schnittstabil/csrf-twig-helpers/badges/gpa.svg)](https://codeclimate.com/github/schnittstabil/csrf-twig-helpers)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/46f79541-4627-48f9-bb9a-92c4f90d02f3/big.png)](https://insight.sensiolabs.com/projects/46f79541-4627-48f9-bb9a-92c4f90d02f3)

> CSRF (Cross-Site Request Forgery) protection helpers for the Twig templating engine.


## Install

```sh
$ composer require schnittstabil/csrf-twig-helpers
```


## Usage

```php
<?php
/**
 * Some callable, which is used to get csrf tokens. E.g:
 */
function getToken() {
    if (!isset($_SESSION['csrf_token'])) {
        // generate a new token...
    }

    return $_SESSION['csrf_token'];
}

$twig = new Twig_Environment($loader);

$twig->addExtension(
    new Schnittstabil\Csrf\Twig\Helpers\Extension(getToken, 'X-XSRF-TOKEN')
);
?>
```

### Template functions
```twig
{{ csrf_token() }}
{# => result of getToken() #}

{{ csrf_token_name() }}
{# => X-XSRF-TOKEN #}

{{ csrf_input_widget() }}
{# => <input name="X-XSRF-TOKEN" type="hidden" value="...some token..." /> #}

{{ csrf_meta_widget() }}
{# => <meta name="X-XSRF-TOKEN" content="...some token..." /> #}
```

## Slim Example

For details see [examples directory](examples/).

### Install Additional Requirements

```sh
$ composer require slim/slim slim/twig-view schnittstabil/psr7-csrf-middleware
```

### Twig Templates

```twig
<!-- index.html.twig -->
<form role="form" method="post" action="{{ path_for('contact') }}">
    <input type="email" name="email" />
    <textarea name="message"></textarea>
    {{ csrf_input_widget() }}
    <button type="submit">Send!</button>
</form>
```

### Scripts

```php
<?php
/* index.php */
require __DIR__ . '/vendor/autoload.php';

use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/**
 * Create App
 */
$app = new Slim\App();

/**
 * Register Csrf Middleware
 */
$app->getContainer()['csrf'] = function ($c) {
    $key = 'This key is not so secret - change it!';

    return CsrfMiddlewareBuilder::create($key)
        ->buildSynchronizerTokenPatternMiddleware();
};
$app->add('csrf');

/**
 * Register Twig Extensions
 */
$app->getContainer()['view'] = function ($c) {
    $view = new Slim\Views\Twig('templates', [
        'cache' => 'cache',
    ]);
    $view->addExtension(new Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));
    $view->addExtension(new Schnittstabil\Csrf\Twig\Helpers\Extension(
        [$c['csrf']->getTokenService(), 'generate']
    ));

    return $view;
};

/**
 * Add routes
 */
$app->get('/', function ($request, $response) {
    return $this->view->render($response, 'index.html.twig');
});

$app->post('/contact', function ($request, $response) {
    return $this->view->render($response, 'contact.html.twig');
})->setName('contact');

/**
 * Run app
 */
$app->run();
?>
```


## License

MIT © [Michael Mayer](http://schnittstabil.de)
