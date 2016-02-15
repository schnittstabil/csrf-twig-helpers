<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/**
 * Create App
 */
$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);


/**
 * Register Csrf Middleware
 */
$app->getContainer()['csrf'] = function () {
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
$app->get('/', function (RequestInterface $request, ResponseInterface $response) {
    return $this->view->render($response, 'index.html.twig');
});

$app->post('/contact', function (RequestInterface $request, ResponseInterface $response) {
    return $this->view->render($response, 'contact.html.twig', $request->getParsedBody());
})->setName('contact');


/**
 * Run app
 */
$app->run();
