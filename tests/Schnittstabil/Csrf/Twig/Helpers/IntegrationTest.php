<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Schnittstabil\Csrf\Twig\Helpers\Extension as CsrfTwigExtension;

/**
 * AcceptHeaderToken tests.
 */
class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    public function getExtensions()
    {
        $tokens = (new \ArrayObject([
            'eyJpYXQiOjE0NTU1NTA1NzUsInR0bCI6MTQ0MCwiZXhwIjoxNDU1NTUyMDE1fQ.qhqpQ9QNhy9il4MkMYHU9r7X91Td8FtyI3HunMZIG2GPEvPQ0kVgqSbg_nhxgrdbq7QgUY2CO_6GcP4cuXd9FQ',
            '<script>console.log("I am nasty!");</script>',
            'eyJpYXQiOjE0NTU1NTA1NzUsInR0bCI6MTQ0MCwiZXhwIjoxNDU1NTUyMDE1fQ.qhqpQ9QNhy9il4MkMYHU9r7X91Td8FtyI3HunMZIG2GPEvPQ0kVgqSbg_nhxgrdbq7QgUY2CO_6GcP4cuXd9FQ',
            '<script>console.log("I am nasty!");</script>',
        ]))->getIterator();

        return array(
            new CsrfTwigExtension(function () use ($tokens) {
                $result = $tokens->current();
                $tokens->next();

                return $result;
            }),
        );
    }

    public function getFixturesDir()
    {
        return dirname(__FILE__).'/Fixtures/';
    }
}
