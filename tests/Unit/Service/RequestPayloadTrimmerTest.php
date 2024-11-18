<?php declare(strict_types=1);

use MichaelCozzolino\PhpRedefinitions\JsonRedefinition;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service\RequestPayloadTrimmer;
use Symfony\Component\HttpFoundation\Request;

covers(RequestPayloadTrimmer::class);

beforeEach(function () {
    $this->jsonRedefinitionMock = Mockery::mock(JsonRedefinition::class);

    $this->requestPayloadTrimmer = new RequestPayloadTrimmer(
        $this->jsonRedefinitionMock
    );
});

test('trim is successful', function (array $parameters, array $trimmedParameters) {
    $request = new Request(content: json_encode($parameters));

    $trimmedPayload = json_encode($trimmedParameters);

    $this->jsonRedefinitionMock->expects('jsonEncode')
                               ->once()
                               ->with($trimmedParameters)
                               ->andReturn($trimmedPayload);

    expect($this->requestPayloadTrimmer->trim($request))->toBe($trimmedPayload);
})->with([
    [
        [
            'name'  => '   a name   ',
            'age'   => 30,
            'price' => '10.1    ',
        ],
        [
            'name'  => 'a name',
            'age'   => 30,
            'price' => '10.1',
        ],
    ],
    [
        [
            'person' => [
                'name'     => 'person name     ',
                'children' => [
                    'age'           => 27,
                    'name'          => ' child name ',
                    'cars'          => ['     golf', 'audi'],
                    'subscriptions' => [
                        'spotify' => [
                            'start' => '20/11/2022  ',
                            'end'   => '20/12/2029',
                            'paid'  => true,
                        ],
                    ],
                ],
            ],
        ],
        [
            'person' => [
                'name'     => 'person name',
                'children' => [
                    'age'           => 27,
                    'name'          => 'child name',
                    'cars'          => ['golf', 'audi'],
                    'subscriptions' => [
                        'spotify' => [
                            'start' => '20/11/2022',
                            'end'   => '20/12/2029',
                            'paid'  => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
]);

test('trim is not successful', function () {
    $content = json_encode(['my' => 'parameters ']);
    $request = new Request(content: $content);

    $this->jsonRedefinitionMock->expects('jsonEncode')
                               ->once()
                               ->with(['my' => 'parameters'])
                               ->andReturn(false);

    expect($this->requestPayloadTrimmer->trim($request))->toBe($content);
});
