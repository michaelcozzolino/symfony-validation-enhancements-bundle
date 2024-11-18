<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Listener\RequestPayloadTrimmerListener;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service\RequestPayloadTrimmer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

covers(RequestPayloadTrimmerListener::class);

beforeEach(function () {
    $this->requestPayloadTrimmerMock = Mockery::mock(RequestPayloadTrimmer::class);

    $this->requestPayloadTrimmerListener = new RequestPayloadTrimmerListener($this->requestPayloadTrimmerMock);
});

test('onKernelRequest is successful', function () {
    $request = new Request(content: '{"my": "   payload here    "}');

    $requestEventMock = Mockery::mock(RequestEvent::class);

    $requestEventMock->expects('getRequest')
                     ->once()
                     ->andReturn($request);

    $trimmedPayload = '{"my": "payload here"} ';

    $this->requestPayloadTrimmerMock->expects('trim')
                                    ->once()
                                    ->with($request)
                                    ->andReturn($trimmedPayload);

    $this->requestPayloadTrimmerListener->onKernelRequest($requestEventMock);

    expect($request->getContent())->toBe($trimmedPayload);

    expect($request->getPayload()->all())->toMatchArray(['my' => 'payload here']);
});
