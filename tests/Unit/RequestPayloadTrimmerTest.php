<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service\RequestPayloadTrimmer;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->requestPayloadTrimmer = new RequestPayloadTrimmer();
});

test('trim', function (array $parameters, array $trimmedParameters) {
    $request = new Request(content: json_encode($parameters));

    expect($this->requestPayloadTrimmer->trim($request))->toBe(json_encode($trimmedParameters));
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
