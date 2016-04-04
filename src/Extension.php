<?php

namespace Schnittstabil\Csrf\Twig\Helpers;

/**
 * CSRF (Cross-Site Request Forgery) protection Twig extension.
 */
class Extension extends \Twig_Extension
{
    /**
     * The token generator.
     *
     * @var callable
     */
    protected $tokenGenerator;

    /**
     * The token name.
     *
     * @var string
     */
    protected $tokenName;

    /**
     * Create a new Extension.
     *
     * @param callable $tokenGenerator the token generator
     * @param string   $tokenName      the token name
     */
    public function __construct(callable $tokenGenerator, $tokenName = 'X-XSRF-TOKEN')
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenName = $tokenName;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'schnittstabil_csrf_twig_helpers_extension';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('csrf_token_name', [$this, 'getTokenName']),
            new \Twig_SimpleFunction('csrf_token', [$this, 'generateCsrfToken']),
            new \Twig_SimpleFunction(
                'csrf_input_widget',
                [$this, 'generateInputWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'csrf_meta_widget',
                [$this, 'generateMetaWidget'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Returns the token name.
     *
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * Generate a new token.
     *
     * @return mixed
     */
    public function generateCsrfToken()
    {
        return call_user_func($this->tokenGenerator);
    }

    /**
     * Generate a new csrf input widget.
     *
     * @param \Twig_Environment $env twig environment needed for escaping
     *
     * @return string
     */
    public function generateInputWidget(\Twig_Environment $env)
    {
        $token = twig_escape_filter($env, $this->generateCsrfToken(), 'html');
        $tokenName = twig_escape_filter($env, $this->getTokenName(), 'html');

        return "<input name=\"$tokenName\" type=\"hidden\" value=\"$token\" />";
    }

    /**
     * Generate a new csrf meta widget.
     *
     * @param \Twig_Environment $env twig environment needed for escaping
     *
     * @return string
     */
    public function generateMetaWidget(\Twig_Environment $env)
    {
        $token = twig_escape_filter($env, $this->generateCsrfToken(), 'html');
        $tokenName = twig_escape_filter($env, $this->getTokenName(), 'html');

        return "<meta name=\"$tokenName\" content=\"$token\" />";
    }
}
