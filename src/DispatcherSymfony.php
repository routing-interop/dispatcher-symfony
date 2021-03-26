<?php

namespace Interop\Routing\Symfony;

use Interop\Routing\DispatcherInterface;
use Interop\Routing\Route\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;

final class DispatcherSymfony implements DispatcherInterface
{
    private UrlMatcherInterface $urlMatcher;

    public function __construct(UrlMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
    }

    public function addRoutes(RouteCollection $routes): self
    {
        $collection = new SymfonyRouteCollection;
        foreach ($routes as $route) {
            $collection->add($route->getName(), new Route($route->getPath(), [], [], [], '', [], $route->getMethods()));
        }
        $this->urlMatcher = new UrlMatcher($collection, RequestContext::fromUri($this->getCurrentURI()));

        return $this;
    }

    public function dispatch(ServerRequestInterface $request): callable
    {
        $params = $this->urlMatcher->match($request->getUri());

        return isset($params['method']) ? [$params['controller'], $params['method']] : $params['controller'];
    }

    private function getCurrentURI(): string
    {
        return
            (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://")
            . $_SERVER['HTTP_HOST']
            . $_SERVER['REQUEST_URI']
        ;
    }
}
