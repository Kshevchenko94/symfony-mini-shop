<?php

namespace App\Security\AuthenticationEntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Twig\Environment;

readonly class ForbiddenEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private Environment $twig,
    ) {}

    /**
     * @inheritDoc
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new Response($this->twig->render(
            'errors/403.html.twig'
        ), Response::HTTP_FORBIDDEN);
    }
}
