<?php

namespace App\EventSubscriber;

use App\Services\Nonce;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class ResponseSubscriber implements EventSubscriberInterface
{
    private $nonce;
    private $event;

    public function __construct(
        Nonce $nonce,
        private UrlGeneratorInterface $router
    ) {
        $this->nonce = $nonce;
    }

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => "onKernelResponse"];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->event = $event;

        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set(
            "Strict-Transport-Security",
            "max-age=31536000 ; includeSubDomains"
        );
        $response->headers->set("X-Frame-Options", "sameorigin");
        $response->headers->set("X-Content-Type-Options", "nosniff");
        $response->headers->set("X-Permitted-Cross-Domain-Policies", "none");
        $response->headers->set("Referrer-Policy", "same-origin");
        $response->headers->set("Clear-Site-Data", '"cache","cookies"');
        $response->headers->set("Cross-Origin-Embedder-Policy", "require-corp");
        $response->headers->set("Cross-Origin-Opener-Policy", "same-origin");
        $response->headers->set("Cross-Origin-Resource-Policy", "same-origin");
        $response->headers->set(
            "Cache-Control",
            "private, max-age=604800, must-revalidate"
        );
        $response->headers->set(
            "Feature-Policy",
            "accelerometer 'none'; ambient-light-sensor 'none'; autoplay 'none'; battery 'none'; camera 'none'; display-capture 'none'; document-domain 'none'; encrypted-media 'none'; fullscreen 'none'; geolocation 'none'; gyroscope 'none'; magnetometer 'none'; microphone 'none'; midi 'none'; navigation-override 'none'; payment 'none'; picture-in-picture 'none'; speaker 'none'; usb 'none'; vibrate 'none'; vr 'none'; " //phpcs:ignore
        );
        $response->headers->set(
            "Content-Security-Policy",
            $this->generateCSP()
        );
    }

    private function generateCSP()
    {
        $csp = [];

        $defaultSrc = ["'self'"];
        $csp[] = "default-src " . implode(" ", $defaultSrc);

        $scriptSrc = [
            "'self'",
            "'strict-dynamic'",
            "'nonce-" . $this->nonce->generate() . "'",
        ];
        if (
            $this->event->getRequest()->getRequestUri() ===
            $this->router->generate("app_petite_vue")
        ) {
            $scriptSrc[] = "'unsafe-eval'"; //add unsafe-eval for petite vue page
        }
        $csp[] = "script-src " . implode(" ", $scriptSrc);

        $styleSrc = [
            "'self'",
            // "'unsafe-inline'",
            "127.0.0.1:5173",
            "*:5173",
            "https://fonts.googleapis.com",
        ];
        $csp[] = "style-src " . implode(" ", $styleSrc);

        $connectSrc = ["'self'", "ws:"];
        $csp[] = "connect-src " . implode(" ", $connectSrc);

        $fontSrc = ["'self'", "https://fonts.gstatic.com"];
        $csp[] = "font-src " . implode(" ", $fontSrc);

        return implode("; ", $csp);
    }
}
