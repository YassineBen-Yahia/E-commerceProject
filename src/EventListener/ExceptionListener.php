<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ExceptionListener
{
    private RouterInterface $router;
    private Environment $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
        $this->router = $router;
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();

            switch ($statusCode) {
                case 401:
                    $response = new RedirectResponse($this->router->generate('app_register'));
                    break;
                case 403:
                    $response = new Response(
                        $this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'),
                        403
                    );
                    break;
                case 404:
                    $response = new Response(
                        $this->twig->render('bundles/TwigBundle/Exception/error404.html.twig'),
                        404
                    );
                    break;
                default:
                    return; // Let Symfony handle other errors
            }

            $event->setResponse($response);
        }
    }
}