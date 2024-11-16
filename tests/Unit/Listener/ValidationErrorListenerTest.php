<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Builder\ValidationErrorBuilder;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Listener\ValidationErrorListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

beforeEach(function () {
    $this->validationErrorBuilderMock = Mockery::mock(ValidationErrorBuilder::class);

    $this->validationErrorListener = new ValidationErrorListener(
        $this->validationErrorBuilderMock,
    );

    $this->eventDispatcher = new EventDispatcher();
    $this->eventDispatcher->addListener('onKernelControllerArguments', [$this->validationErrorListener, 'onKernelControllerArguments']);
    $this->eventDispatcher->addListener('onKernelException', [$this->validationErrorListener, 'onKernelException']);
});

it('sets the response with the correct validation errors', function () {
    $kernelMock  = Mockery::mock(HttpKernelInterface::class);
    $requestMock = Mockery::mock(Request::class);
    $requestType = 1;

    $controllerArgumentsEvent = new ControllerArgumentsEvent(
        $kernelMock,
        new ControllerEvent(
            $kernelMock,
            fn() => true,
            $requestMock,
            $requestType
        ),
        [new MapRequestPayload()],
        $requestMock,
        $requestType
    );

    $violationsMock        = Mockery::mock(ConstraintViolationListInterface::class);
    $previousExceptionMock = Mockery::mock(ValidationFailedException::class);

    $previousExceptionMock->expects('getViolations')
                          ->once()
                          ->andReturn($violationsMock);

    $validationErrors = ['my' => ['errors']];

    $this->validationErrorBuilderMock->expects('build')
                                     ->once()
                                     ->with($violationsMock)
                                     ->andReturn($validationErrors);

    $exception = new UnprocessableEntityHttpException(previous: $previousExceptionMock);

    $exceptionEvent = new ExceptionEvent(
        $kernelMock,
        $requestMock,
        $requestType,
        $exception
    );

    $this->eventDispatcher->dispatch($controllerArgumentsEvent, 'onKernelControllerArguments');
    $this->eventDispatcher->dispatch($exceptionEvent, 'onKernelException');

    $actualResponse = $exceptionEvent->getResponse();

    expect($actualResponse->getContent())->toBe(json_encode($validationErrors));
    expect($actualResponse->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it(
    'does not set any response when the request is not the main one',
    function (int $controllerArgumentsEventRequestType, int $exceptionEventRequestType) {
        $kernelMock  = Mockery::mock(HttpKernelInterface::class);
        $requestMock = Mockery::mock(Request::class);

        $controllerArgumentsEvent = new ControllerArgumentsEvent(
            $kernelMock,
            new ControllerEvent(
                $kernelMock,
                fn() => true,
                $requestMock,
                $controllerArgumentsEventRequestType
            ),
            [new MapRequestPayload()],
            $requestMock,
            $controllerArgumentsEventRequestType
        );

        $previousExceptionMock = Mockery::mock(ValidationFailedException::class);

        $previousExceptionMock->expects('getViolations')->never();

        $this->validationErrorBuilderMock->expects('build')->never();

        $exception = new UnprocessableEntityHttpException(previous: $previousExceptionMock);

        $exceptionEvent = new ExceptionEvent(
            $kernelMock,
            $requestMock,
            $exceptionEventRequestType,
            $exception
        );

        $this->eventDispatcher->dispatch($controllerArgumentsEvent, 'onKernelControllerArguments');
        $this->eventDispatcher->dispatch($exceptionEvent, 'onKernelException');
    })->with([
    [1, 2],
    [2, 1],
    [2, 2],
]);

it('does not set any response when there is no MapRequestPayload or MapQueryString', function () {
    $kernelMock  = Mockery::mock(HttpKernelInterface::class);
    $requestMock = Mockery::mock(Request::class);

    $controllerArgumentsEvent = new ControllerArgumentsEvent(
        $kernelMock,
        new ControllerEvent(
            $kernelMock,
            fn() => true,
            $requestMock,
            1
        ),
        [],
        $requestMock,
        1
    );

    $previousExceptionMock = Mockery::mock(ValidationFailedException::class);

    $previousExceptionMock->expects('getViolations')->never();

    $this->validationErrorBuilderMock->expects('build')->never();

    $exception = new UnprocessableEntityHttpException(previous: $previousExceptionMock);

    $exceptionEvent = new ExceptionEvent(
        $kernelMock,
        $requestMock,
        1,
        $exception
    );

    $this->eventDispatcher->dispatch($controllerArgumentsEvent, 'onKernelControllerArguments');
    $this->eventDispatcher->dispatch($exceptionEvent, 'onKernelException');
});

it('does not set any response when the exception is not an http exception', function () {
    $kernelMock  = Mockery::mock(HttpKernelInterface::class);
    $requestMock = Mockery::mock(Request::class);

    $controllerArgumentsEvent = new ControllerArgumentsEvent(
        $kernelMock,
        new ControllerEvent(
            $kernelMock,
            fn() => true,
            $requestMock,
            1
        ),
        [new MapRequestPayload()],
        $requestMock,
        1
    );

    $previousExceptionMock = Mockery::mock(ValidationFailedException::class);

    $previousExceptionMock->expects('getViolations')->never();

    $this->validationErrorBuilderMock->expects('build')->never();

    $exception = new Exception(previous: $previousExceptionMock);

    $exceptionEvent = new ExceptionEvent(
        $kernelMock,
        $requestMock,
        1,
        $exception
    );

    $this->eventDispatcher->dispatch($controllerArgumentsEvent, 'onKernelControllerArguments');
    $this->eventDispatcher->dispatch($exceptionEvent, 'onKernelException');
});

it('does not set any response when the previous exception is not a validation failed exception', function () {
    $kernelMock  = Mockery::mock(HttpKernelInterface::class);
    $requestMock = Mockery::mock(Request::class);

    $controllerArgumentsEvent = new ControllerArgumentsEvent(
        $kernelMock,
        new ControllerEvent(
            $kernelMock,
            fn() => true,
            $requestMock,
            1
        ),
        [new MapRequestPayload()],
        $requestMock,
        1
    );

    $previousExceptionMock = Mockery::mock(Exception::class);

    $this->validationErrorBuilderMock->expects('build')->never();

    $exception = new UnprocessableEntityHttpException(previous: $previousExceptionMock);

    $exceptionEvent = new ExceptionEvent(
        $kernelMock,
        $requestMock,
        1,
        $exception
    );

    $this->eventDispatcher->dispatch($controllerArgumentsEvent, 'onKernelControllerArguments');
    $this->eventDispatcher->dispatch($exceptionEvent, 'onKernelException');
});
