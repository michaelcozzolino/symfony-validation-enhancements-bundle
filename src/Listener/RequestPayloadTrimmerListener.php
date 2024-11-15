<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Listener;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service\RequestPayloadTrimmer;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @psalm-api
 */
class RequestPayloadTrimmerListener
{
    public function __construct(protected readonly RequestPayloadTrimmer $requestPayloadTrimmer)
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $this->requestPayloadTrimmer->trim($request)
        );
    }
}
