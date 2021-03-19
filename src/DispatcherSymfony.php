<?php

namespace Interop\Routing\Symfony;

use Interop\Routing\DispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class DispatcherSymfony implements DispatcherInterface
{
    private UrlMatcherInterface $urlMatcher;

    public function __construct(UrlMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
    }

    public function dispatch(ServerRequestInterface $request): callable
    {
        $params = $this->urlMatcher->match($request->getUri());

        return isset($params['method']) ? [$params['controller'], $params['method']] : $params['controller'];
    }
}
