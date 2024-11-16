<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Listener;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Builder\ValidationErrorBuilder;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * @psalm-api
 */
class ValidationErrorListener
{
    private ?int $validationErrorResponseStatusCode = null;

    public function __construct(
        protected readonly ValidationErrorBuilder $validationErrorBuilder,
    ) {
    }

    #[AsEventListener(event: ExceptionEvent::class)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previous  = $exception->getPrevious();

        if (
            $event->isMainRequest() === false ||
            $this->validationErrorResponseStatusCode === null ||
            $exception instanceof HttpException === false ||
            $previous instanceof ValidationFailedException === false
        ) {
            return;
        }

        $errors = $this->validationErrorBuilder->build($previous->getViolations());

        $response = new JsonResponse(
            $errors,
            $this->validationErrorResponseStatusCode,
        );

        $event->setResponse($response);
    }

    #[AsEventListener(event: ControllerArgumentsEvent::class)]
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        if ($event->isMainRequest() === false) {
            return;
        }

        /**
         * @psalm-suppress MixedAssignment The array is not type hinted from symfony
         */
        foreach ($event->getArguments() as $eventArgument) {
            if ($eventArgument instanceof MapRequestPayload || $eventArgument instanceof MapQueryString) {
                $this->validationErrorResponseStatusCode = $eventArgument->validationFailedStatusCode;

                break;
            }
        }
    }
}
