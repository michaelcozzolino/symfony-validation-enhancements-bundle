<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Builder\ValidationErrorBuilder;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

beforeEach(function () {
    $this->validationErrorBuilder = new ValidationErrorBuilder();
});

dataset('validation errors', function () {
    $propertyPath1     = 'name';
    $violationMessage1 = 'name is not valid';

    $propertyPath2     = 'person.name';
    $violationMessage2 = 'person name is not valid';

    $propertyPath3     = 'person.parent.name';
    $violationMessage3 = 'parent person name is not valid';

    $propertyPath4     = 'person.children[2].name';
    $violationMessage4 = 'children name is not valid';

    return [
        [
            $propertyPath1,
            $violationMessage1,
            [
                $propertyPath1 => [$violationMessage1],
            ],
        ],
        [
            $propertyPath2,
            $violationMessage2,
            [
                'person' => [
                    'name' => [$violationMessage2],
                ],
            ],
        ],
        [
            $propertyPath3,
            $violationMessage3,
            [
                'person' => [
                    'parent' => [
                        'name' => [$violationMessage3],
                    ],
                ],
            ],
        ],
        [
            $propertyPath4,
            $violationMessage4,
            [
                'person' => [
                    'children' => [
                        2 => [
                            'name' => [$violationMessage4],
                        ],
                    ],
                ],
            ],
        ],
    ];
});

test('build', function (string $propertyPathName, string $violationMessage, array $expectedErrors) {
    $violationMock = Mockery::mock(ConstraintViolation::class);

    $violationMock->expects('getPropertyPath')
                  ->once()
                  ->andReturn($propertyPathName);

    $violationMock->expects('getMessage')
                  ->once()
                  ->andReturn($violationMessage);

    $violationMocks = new ConstraintViolationList([$violationMock]);

    $actualErrors = $this->validationErrorBuilder->build($violationMocks);

    expect($expectedErrors)->toBe($actualErrors);
})->with('validation errors');
